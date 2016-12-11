<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 17:55
 */

namespace App\Service;

use App\Constant\ACTION_TYPE;
use App\Constant\ALGO_CONST;
use App\Constant\DATE_FORMAT;
use App\Constant\JSON_KEY;
use App\Constant\RESULT_CODE;
use App\Model\Entity\GameTurn;
use App\Model\Vo\CheckCurrentTurnResult;
use App\Model\Vo\CreateTurnResult;
use App\Service;
use App\Model\Vo\GameTurnHistoriesResult;
use App\Model\Vo\ProcessMyTurnResult;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\Exception\MissingEntityException;
use Cake\ORM\TableRegistry;

class GameTurnService {


    /**
     * @param $gameId
     * @param $gameAiId
     * @param $currentCount
     * @param $canStay
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
     * @param $gameAIId
     * @param $turnId
     * @param $actionType
     * @param int $attackCardId
     * @param int $targetCardId
     * @param int $targetCardNumber
     * @return ProcessMyTurnResult
     */
    public function doTurnAction($gameId, $gameAIId, $turnId, $actionType, $attackCardId = 0, $targetCardId = 0, $targetCardNumber = 0 )
    {
        $games = TableRegistry::get('games');
        $gameRecord = $games->get($gameId,['contain'=>['CurrentGameTurns']]);
        $gameTurns = TableRegistry::get('game_turns');
        $currentTurnRecord = $gameRecord->current_game_turn;


        if(empty($currentTurnRecord) || $currentTurnRecord->id != $turnId)
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_INVALID_TURN_ID);



        //1.The turn is over.
        if($currentTurnRecord->is_finished)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_ALREADY_FINISHED);
        }

        //2.Not my turn
        if($currentTurnRecord->game_ai_id != $gameAIId)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_NOT_MY_TURN);
        }


        //3.Cannot stay
        if($actionType == ACTION_TYPE::STAY && $currentTurnRecord->can_stay == false)
        {
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_CANNOT_STAY);
        }

        //4.Turn is in progress.
        //Check previous turn progress
        if($this->isLockedForGameId($gameId) ){
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_PREVIOUS_TURN_LOCK);
        }

        $this->createLockFile($gameId);

        $opponentGameAIId = ($gameRecord->team_a_ai_id == $gameAIId)?$gameRecord->team_b_ai_id:$gameRecord->team_a_ai_id;
        $nextTurnCanStay = false;
        $nextTurnCurrentCount = $currentTurnRecord->current_count + 1;

        $currentTurnRecord->target_number = $targetCardNumber;
        $currentTurnRecord->turn_action_code = $actionType;
        $currentTurnRecord->is_finished = true;

        //攻撃カードの検証およびターゲットカードの検証
        //Get Card Status
        $gameCardService = new GameCardService();
        if(!$gameCardService->validateAttackCard($attackCardId)){
            return new ProcessMyTurnResult(RESULT_CODE::PROCESS_MY_TURN_FAILED_NOT_VALID_ATTACK_CARD_ID);
        }


        //5. Process  turn
        //Check stay or attack
        $winnerGameAIId = ALGO_CONST::UNKNOWN;
        if($actionType == ACTION_TYPE::ATTACK){
            //Do attack
            $gameCardService = new GameCardService();
            $isSuccessAttack = $gameCardService->attack($gameAIId,$attackCardId,$targetCardId,$targetCardNumber);
            $currentTurnRecord->attack_game_card_id = $attackCardId;
            $currentTurnRecord->target_game_card_id = $targetCardId;
            $currentTurnRecord->is_stay = false;
            $currentTurnRecord->is_success_attack = $isSuccessAttack;
            $currentTurnRecord->turn_ended = date(DATE_FORMAT::READABLE_DATE);
            $nextTurnGameAIId = ($isSuccessAttack)?$gameAIId:$opponentGameAIId;
            $nextTurnCanStay = $isSuccessAttack;
            //update card status
            $currentCardStatusService = $gameCardService->getCurrentDistributedCards($gameId);
            if($isSuccessAttack){
                //Check game is over.
                $winnerGameAIId = $currentCardStatusService->getWinningGameAIId();
                //Game Finished
                if($winnerGameAIId != ALGO_CONST::UNKNOWN){
                    $gameRecord->win_ai_id = $winnerGameAIId;
                    $gameRecord->is_finished = true;
                    $gameRecord->fisnied_datetime = date(DATE_FORMAT::READABLE_DATE);
                    $games->save($gameRecord,['validate'=>false]);
                }else{
                    $currentCardStatusService->createAttackCard($nextTurnGameAIId);
                }
            }



            //Stay
        }else{
            $currentTurnRecord->is_stay = true;
            $currentTurnRecord->is_success_attack = false;
            $currentTurnRecord->turn_ended = date(DATE_FORMAT::READABLE_DATE);
            $nextTurnGameAIId = $opponentGameAIId;

            //Update Previous Attack Card.
            $gameCardService = new GameCardService();
            $gameCardService->turnOffAttackCardForGame($gameId);
            //todo : GameCard Serviceと GameCardStatusServiceは統合させる。

            //Create New Attack Card
            $currentCardStatusService = $gameCardService->getCurrentDistributedCards($gameId);
            $currentCardStatusService->createAttackCard($nextTurnGameAIId);

        }
        $gameTurns->save($currentTurnRecord,['validate' => false]);


        //Winner does not Exists.
        //Create Next Turn.
        if($winnerGameAIId == ALGO_CONST::UNKNOWN){
            //Next Turn Record
            $nextTurnInfo = $this->createTurn($gameId,$nextTurnGameAIId,$nextTurnCurrentCount,$nextTurnCanStay,$currentCardStatusService);

            //Update current game record.
            $gameRecord->current_ai_id = $nextTurnGameAIId;
            $gameRecord->current_turn_id = $nextTurnInfo->getTurnId();
            $games->save($gameRecord);
        }

        //Remove Lock Status.
        //$this->unLock($gameId);
        $checkCurrentTurnResult = (new GameTurnService())->checkCurrentTurn($gameId,$gameAIId);
        return $checkCurrentTurnResult;
    }

    /**
     * @param $gameId
     * @param $gameAIId
     * @return GameTurnHistoriesResult
     */
    public function getTurnHistoryResult($gameId,$gameAIId)
    {
        $gameTurns = TableRegistry::get('game_turns');
        $turnHistories = $gameTurns->find()->where(['game_turns.game_id'=>$gameId])->order(['game_turns.id'])->contain(['TargetGameCards','AttackGameCards'])->all();
        $gameTurnHistoriesResult = new GameTurnHistoriesResult(RESULT_CODE::SUCCESS);
        $gameTurnHistoriesResult->setHistories($turnHistories);
        $gameTurnHistoriesResult->setGameId($gameId);
        $gameTurnHistoriesResult->setGameAIId($gameAIId);
        return $gameTurnHistoriesResult;
    }


    /**
     * @param $gameId
     * @param $gameAIId
     * @return CheckCurrentTurnResult
     * @throws \Psy\Exception\ErrorException
     */
    public function checkCurrentTurn($gameId, $gameAIId)
    {
        //Game Info
        $gameEntity = (new GameService())->getGameEntity($gameId);
        $gameTurnEntity = $gameEntity->current_game_turn;
//        $gameTurnEntity = $gameEntity->game_
//
//        if($gameTurnEntity == null){
//           return  new CheckCurrentTurnResult(RESULT_CODE::CHECK_CURRENT_TURN_FAILED);
//        }

        //現在のカード情報
        $currentCardStatus = (new GameCardService())->getCurrentDistributedCards($gameId);

        //ターン履歴情報
        $turnHistories = $this->getTurnHistoryResult($gameId,$gameAIId);
        $isPreviousTurnProcessing = $this->isLockedForGameId($gameId);
        $checkCurrentTurnResult = new CheckCurrentTurnResult(RESULT_CODE::SUCCESS);
        $checkCurrentTurnResult->setGameEntity($gameEntity);
        $checkCurrentTurnResult->setCurrentCardStatus($currentCardStatus);
        $checkCurrentTurnResult->setGameTurnHistoriesResult($turnHistories);
        $checkCurrentTurnResult->setGameAIId($gameAIId);
        $checkCurrentTurnResult->setIsProcessing($isPreviousTurnProcessing);
        $checkCurrentTurnResult->setWinnerGameAIId($gameEntity->win_ai_id);
        if($gameTurnEntity != null)  $checkCurrentTurnResult->setCanStayThisTurn($gameTurnEntity->can_stay);
        else $checkCurrentTurnResult->setCanStayThisTurn(false);
        //todo
        //Game 終了判定を追加
        return $checkCurrentTurnResult;
    }

    /**
     * @param $gameTurnId
     * @return \Cake\Datasource\EntityInterface|mixed
     */
    public function getGameTurnEntity($gameTurnId)
    {
        $gameTurns = TableRegistry::get('game_turns');
        try {
            return $gameTurns->get($gameTurnId);
        }catch (MissingEntityException $e){
            return null;
        }
    }

    /**
     * @param $gameId
     * @return bool
     */
    private function isLockedForGameId($gameId)
    {
        $dir = new Folder(TMP);
        if(count($dir->find($this->getLockFileName($gameId))) != 0){
            return true;
        }else{
            return false;
        }
    }

    private function createLockFile($gameId)
    {
        //todo Handle exception
        new File(TMP.DS.$this->getLockFileName($gameId));

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