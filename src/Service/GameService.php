<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 16:36
 */

namespace App\Service;


use App\Constant\DATE_FORMAT;
use App\Constant\MATCHING_TYPE;
use App\Constant\RESULT_CODE;
use App\Core\Config;
use App\Model\Vo\CheckMatchingResult;
use App\Model\Vo\InitGameResult;
use Symfony\Component\Config\Definition\Exception\Exception;
use Cake\ORM\TableRegistry;

class GameService {

    public function initGame($groupId,$gameAICode){
        //Find game id
        //まだ、マッチングされていない Game IDもしくは、新規ゲームID
        $games = TableRegistry::get('games');

        switch(Config::CURRENT_MATCHING_TYPE){
            case MATCHING_TYPE::SAME_TEAM:
                $findQuery = $games->find()->where(
                    ['is_finished'=>false,
                        'OR'=>[
                            ['team_a_group_id'=>$groupId,'team_b_group_id'=>0],
                            ['team_b_group_id'=>$groupId,'team_a_group_id'=>0],

                        ]
                    ]
                );

                break;
            case MATCHING_TYPE::DIFF_TEAM:

                $findQuery = $games->find()->where(
                    ['is_finished'=>false,
                        'OR'=>[
                            ['team_a_group_id <> '=>$groupId,'team_b_group_id'=>0],
                            ['team_b_group_id <> '=>$groupId,'team_a_group_id'=>0],

                        ]
                    ]
                );
                break;

            default :
                throw new Exception("現在のマッチングタイプを確認できません。");


            break;
        }
        $record = $findQuery->first();

        //Create New Game
        if($record == null){
            $currentDate = date(DATE_FORMAT::READABLE_DATE);
            $entity = $games->newEntity();
            $entity->team_a_group_id = $groupId;
            $entity->created = $currentDate;
            $entity->modified = $currentDate;
            $record = $games->save($entity);
            $gameId = $record->id;

            //Create New AI
            $gameAIId = (new GameAIService())->getNewAI($groupId,$gameId,$gameAICode);
            //Update Records
            $updateQuery = $games->query();
            $updateQuery
                ->update()
                ->set(['team_a_ai_id'=>$gameAIId])
                ->where(['id'=>$gameId])
                ->execute();

        }


        else //Update Game. - Pairwise done.
        {

            $gameId = $record->id;
            $gameAIId = (new GameAIService())->getNewAI($groupId,$gameId,$gameAICode);

            //Check who first
            $startAIId = ($this->isATeamFirst())?$record->team_a_ai_id:$gameAIId;

            //Distributes cards.
            $cardDistributeResult = (new GameCardService())->initDistributesCardForGame($gameId,$record->team_a_ai_id,$gameAIId);

            //Create first turn.
            //Todo Handling exception.
            $turnResult = (new GameTurnService())->createTurn($gameId,$startAIId,1,false);
            $updateDate = date(DATE_FORMAT::READABLE_DATE);
            $updateQuery = $games->query();
            $updateQuery
                ->update()
                ->set( ['team_b_group_id'=>$groupId,
                    'team_b_ai_id'=>$gameAIId,
                    'modified'=>$updateDate,
                    'start_ai_id'=>$startAIId,
                    'current_turn_id'=>$turnResult->getTurnId(),
                    'current_ai_id'=>$startAIId])
                ->where(["id"=>$gameId])
                ->execute();

            if($cardDistributeResult->getCode() !== RESULT_CODE::SUCCESS){
                return new InitGameResult($cardDistributeResult->getCode());
            }
        }

        $initGameResult = new InitGameResult(RESULT_CODE::SUCCESS);
        $initGameResult->setGameAIId($gameAIId);
        $initGameResult->setGameId($gameId);

        return $initGameResult;
    }

    /**
     * 該当ゲームの両チームが揃ったらマッチング成功、揃ってなければ待機中を返す。
     * @param int $groupId
     * @param int $gameId
     * @param int $gameAiId
     * @return App\Model\Vo\CheckMatchingResult
     */
    public function checkMatching($groupId,$gameId,$gameAiId)
    {
        $games = TableRegistry::get('games');
        $record = $games->get($gameId);

        if($record->team_a_group_id != $groupId && $record->team_b_group_id != $groupId)
        {
            return new CheckMatchingResult(RESULT_CODE::MATCHING_INVALID_GROUP_ID,$gameAiId);
        }

        if($record->team_a_ai_id != $gameAiId && $record->team_b_ai_id != $gameAiId)
            return new CheckMatchingResult(RESULT_CODE::MATCHING_INVALID_AI_ID,$gameAiId);

        if($record->team_a_group_id != 0 && $record->team_b_group_id != 0 ){
            $checkMatchingResult = new CheckMatchingResult(RESULT_CODE::SUCCESS,$gameAiId);
            $checkMatchingResult->setIsFinishedMatching(true);
            $checkMatchingResult->setIsMyTurn(($record->current_ai_id == $gameAiId));
            $checkMatchingResult->setTurnId($record->current_turn_id);
            $checkMatchingResult->setCardLists((new GameCardService())->getCurrentDistributedCards($gameId));
            return $checkMatchingResult;

        }else{

            $checkMatchingResult = new CheckMatchingResult(RESULT_CODE::SUCCESS,$gameAiId);
            $checkMatchingResult->setIsFinishedMatching(false);
            return $checkMatchingResult;
        }
    }

    private function isATeamFirst()
    {
        return (mt_rand(0,1) == 0);
    }



}