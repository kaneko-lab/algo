<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 17:55
 */

namespace App\Service;

use App\Constant\ACTION_TYPE;
use App\Constant\DATE_FORMAT;
use App\Constant\RESULT_CODE;
use App\Model\Vo\CreateTurnResult;
use App\Model\Vo\CurrentCardStatus;
use App\Model\Vo\ProcessMyTurnResult;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;

class GameTurnService {




    /**
     * @param $gameId
     * @param $gameAiId
     * @param $currentCount
     * @param $canStay
     * @param $turnStatusData
     * @return CreateTurnResult
     */
    public function createTurn($gameId,$gameAiId, $currentCount, $canStay)
    {
        $gameTurns = TableRegistry::get('game_turns');
        $entity = $gameTurns->newEntity();
        $entity->game_id = $gameId;
        $entity->game_ai_id = $gameAiId;
        $entity->current_count = $currentCount;
        $entity->can_stay = $canStay;
        $record = $gameTurns->save($entity);
        $createTurnResult = new CreateTurnResult(RESULT_CODE::SUCCESS);
        $createTurnResult->setTurnData($record->id,$record);
        return $createTurnResult;
    }


    /**
     * @param $gameId
     * @param $aiId
     * @param $turnId
     * @param $actionType
     * @param int $sourceCardId
     * @param int $targetCardId
     * @param int $targetCardNumber
     * @return ProcessMyTurnResult
     */
    public function processMyTurn($gameId, $aiId, $turnId, $actionType, $sourceCardId = 0, $targetCardId = 0, $targetCardNumber = 0 )
    {

        $gameTurns = TableRegistry::get('game_turns');
        $turnRecord = $gameTurns->get($turnId);

        //1.Not my turn
        if($turnRecord->game_ai_id != $aiId)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_NOT_MY_TURN);
        }

        //2.The turn is over.
        if($turnRecord->is_finished)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_ALREADY_FINISHED);
        }
        //3.Cannot stay
        if($actionType == ACTION_TYPE::STAY && $turnRecord->is_stay == false)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_CANNOT_STAY);
        }

        //4.Turn is in progress.
        //Check previous turn progress
        if($this->checkLockStatus($gameId) == false ){
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_PREVIOUS_TURN_LOCK);
        }

        $games = TableRegistry::get('games');
        $gameRecord = $games->get($gameId);
        $opponentAiId = ($gameRecord->team_a_ai_id == $aiId)?$gameRecord->team_b_ai_id:$gameRecord->team_a_ai_id;
        $nextTurnCanStay = false;
        $nextTurnCurrentCount = $turnRecord->current_count + 1;

        $turnRecord->target_number = $targetCardNumber;
        $turnRecord->turn_action_code = $actionType;
        $turnRecord->is_finished = true;

        //5. Process  turn
        //Check stay or attack
        if($actionType == ACTION_TYPE::ATTACK){
            //Do attack
            $gameCardService = new GameCardService();
            $isSuccessAttack = $gameCardService->attack($aiId,$sourceCardId,$targetCardId,$targetCardNumber);
            $turnRecord->attack_game_card_id = $sourceCardId;
            $turnRecord->target_game_card_id = $targetCardId;
            $turnRecord->is_stay = false;
            $turnRecord->is_success_attack = $isSuccessAttack;
            $turnRecord->turn_ended = date(DATE_FORMAT::READABLE_DATE);
            $nextTurnAiId = ($isSuccessAttack)?$aiId:$opponentAiId;

        //Stay
        }else{
            $turnRecord->attack_game_card_id = 0;
            $turnRecord->target_game_card_id = 0;
            $turnRecord->is_stay = true;
            $turnRecord->is_success_attack = false;
            $turnRecord->turn_ended = date(DATE_FORMAT::READABLE_DATE);
            $nextTurnAiId = $opponentAiId;

        }
        $gameTurns->save($turnRecord);

        //Get Card Status
        $gameCardService = new GameCardService();
        $currentCardStatus = $gameCardService->getCurrentDistributedCards($gameId);

        //Check game is over.
        $winnerId = $currentCardStatus->getWinningAiId();
        $processMyTurnResult = new ProcessMyTurnResult(RESULT_CODE::SUCCESS);
        $processMyTurnResult->setCurrentCardStatus($currentCardStatus);
        //Winner does not Exists.
        if($winnerId == 0){
            //Next Turn Record
            $nextTurnInfo = $this->createTurn($gameId,$nextTurnAiId,$nextTurnCurrentCount,$nextTurnCanStay,$currentCardStatus);

            //Update current game record.
            $gameRecord->current_ai_id = $nextTurnAiId;
            $gameRecord->current_turn_id = $nextTurnInfo->getTurnId();
            $games->save($gameRecord);
        }else{
            $processMyTurnResult->setWinnerAiId($winnerId);
            $gameRecord->is_finished = true;
            $gameRecord->win_ai_id = $winnerId;
            $gameRecord->finshed_datetime = date(DATE_FORMAT::READABLE_DATE);
            $games->save($gameRecord);
        }

        //Remove Lock Status.
        //$this->unLock($gameId);
        return $processMyTurnResult;
    }


    /**
     * @param $gameId
     * @return \Cake\Datasource\ResultSetInterface
     */
    public function getTurnHistory($gameId)
    {
        $gameTurns = TableRegistry::get('game_turns');
        $turnHistories = $gameTurns->find()->where(['game_turns.game_id'=>$gameId])->order(['game_turns.id'])->contain(['TargetGameCards','AttackGameCards'])->all();
        return $turnHistories;
    }

    /**
     * 現在のターンが私のターンなのか、相手のターンなのかを確認
     * 自分のターンである場合はSTAY可能なのかも確認
     * @param $groupId
     * @param $gameId
     */
    public function checkCurrentTurn($groupId, $gameId)
    {

    }

    /**
     * @param $gameId
     * @return bool
     */
    private function checkLockStatus($gameId)
    {
        $dir = new Folder(TMP);
        if(count($dir->find($this->getLockFileName($gameId))) != 0){
            return false;
        }else{
            //todo Handle exception
            new File(TMP.DS.$this->getLockFileName($gameId));
            return true;
        }
    }

    private function unLock($gameId)
    {
        $file = new File(TMP.DS.$this->getLockFileName($gameId));
        $file->delete();
    }
    private function getLockFileName($gameId)
    {
        return $gameId.'.turn.lock';
    }
}