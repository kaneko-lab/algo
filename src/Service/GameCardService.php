<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 21:56
 */

namespace App\Service;


use App\Constant\GAME_CARD;
use App\Constant\RESULT_CODE;
use Cake\ORM\TableRegistry;
use App\Service\CurrentCardStatusService;
use Psy\Exception\ErrorException;
use App\Model\Entity\GameCard;

class GameCardService {

    /**
     * @param $gameId
     * @param $gameAIID1
     * @param $gameAIID2
     * @return CurrentCardStatusService|null
     */
    public function initDistributesCardForGame($gameId,$gameAIID1,$gameAIID2)
    {

        $result = $this->getCurrentDistributedCards($gameId);
        if($result != null)
            return $result;

        //Shuffle Card.
        $cardIDList = [
            GAME_CARD::WHITE_0,
            GAME_CARD::WHITE_1,
            GAME_CARD::WHITE_2,
            GAME_CARD::WHITE_3,
            GAME_CARD::WHITE_4,
            GAME_CARD::WHITE_5,
            GAME_CARD::WHITE_6,
            GAME_CARD::WHITE_7,
            GAME_CARD::WHITE_8,
            GAME_CARD::WHITE_9,
            GAME_CARD::WHITE_10,
            GAME_CARD::WHITE_11,
            GAME_CARD::BLACK_0,
            GAME_CARD::BLACK_1,
            GAME_CARD::BLACK_2,
            GAME_CARD::BLACK_3,
            GAME_CARD::BLACK_4,
            GAME_CARD::BLACK_5,
            GAME_CARD::BLACK_6,
            GAME_CARD::BLACK_7,
            GAME_CARD::BLACK_8,
            GAME_CARD::BLACK_9,
            GAME_CARD::BLACK_10,
            GAME_CARD::BLACK_11
        ];

        shuffle($cardIDList);

        //最初の4枚をAチームへ
        //次の4枚をBチームへ
        //残りはすべて山へ、

        $gameCards = TableRegistry::get('game_cards');




        for($i = 0 ; $i < 4 ; $i ++){
            $entity = $gameCards->newEntity();
            $entity->game_id = $gameId;
            $entity->card_id = $cardIDList[$i];
            $entity->owner_ai_id = $gameAIID1;
            $entity->is_visible =false;
            $gameCards->save($entity);
        }


        for($i = 4; $i < 8 ; $i ++){
            $entity = $gameCards->newEntity();
            $entity->game_id = $gameId;
            $entity->card_id = $cardIDList[$i];
            $entity->owner_ai_id = $gameAIID2;
            $entity->is_visible = false;
            $gameCards->save($entity);

        }

        for($i = 8; $i < count($cardIDList) ; $i ++){
            $entity = $gameCards->newEntity();
            $entity->game_id = $gameId;
            $entity->card_id = $cardIDList[$i];
            $entity->owner_ai_id = 0; // Deck
            $entity->is_visible = false;
            $gameCards->save($entity);
        }

        return $this->getCurrentDistributedCards($gameId);
    }


    /**
     * @param $gameId
     * @return CurrentCardStatusService|null
     * @throws ErrorException
     */
    public function getCurrentDistributedCards($gameId)
    {
        $gameCards = TableRegistry::get('game_cards');
        // コントローラやテーブルのメソッド内で
        $query = $gameCards
                    ->find('all')
                    ->where(['game_id ' => $gameId])
                    ->contain(['Cards'])
                    ->order('Cards.order');

        if($query->count()==0){
            return null;
        }else{

            if($query->count()<24){
                throw new ErrorException("Wrong total card number for game " . $gameId);
            }

            $currentCardStatusService = new CurrentCardStatusService();
            foreach($query as $row){
                $currentCardStatusService->addCard($row->owner_ai_id ,$row);
            }
            return $currentCardStatusService;
        }
    }



    /**
     * @param $gameAIId
     * @param $attackCardId
     * @param $targetCardId
     * @param $number
     * @return bool
     */
    public function attack($gameAIId, $attackCardId, $targetCardId,$number)
    {
        $attackResult = false;
        $gameCards = TableRegistry::get('game_cards');
        $targetCardRecord = $gameCards->get($targetCardId,['contain'=>['Cards']]);
        $attackCardRecord = $gameCards->newEntity();
        $attackCardRecord->is_current_attack_card = true;

        //Success Attack
        if($targetCardRecord->card->number == $number){
            $targetCardRecord->is_visible = true;
            $gameCards->save($targetCardRecord);
            $attackResult = true;
        }
        //Failed Attack
        else {
            $attackCardRecord->is_visible = true;
            $attackCardRecord->is_current_attack_card = false;
        }

        $attackCardRecord->id = $attackCardId;
        $attackCardRecord->owner_ai_id = $gameAIId;
        $gameCards->save($attackCardRecord);
        return $attackResult;
    }


    /**
     * @param $attackCardId
     * @return bool
     */
    public function validateAttackCard($attackCardId){
        $gameCards = TableRegistry::get('game_cards');
        $gameCardEntity = $gameCards->get($attackCardId);
        if($gameCardEntity == null || !$gameCardEntity->is_current_attack_card )
            return false;
        return true;


    }

    /**
     * @param $gameId
     */
    public function turnOffAttackCardForGame($gameId){
        $gameCards = TableRegistry::get('game_cards');
        $gameCards->updateAll(['is_current_attack_card'=>false],['game_id'=>$gameId]);
    }
}