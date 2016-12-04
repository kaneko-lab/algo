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
use App\Model\Vo\CardDistributeStatus;
class CardService {

    /**
     * @param $gameId
     * @param $aiID1
     * @param $aiID2
     * @return CardDistributeStatus|null
     */
    public function initDistributesCardForGame($gameId,$aiID1,$aiID2)
    {

        $result = $this->getCurrentDistributedCards($gameId,$aiID1);
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
            $entity->owner_ai_id = $aiID1;
            $entity->is_visible =false;
            $record = $gameCards->save($entity);
        }


        for($i = 4; $i < 8 ; $i ++){
            $entity = $gameCards->newEntity();
            $entity->game_id = $gameId;
            $entity->card_id = $cardIDList[$i];
            $entity->owner_ai_id = $aiID2;
            $entity->is_visible =false;
            $gameCards->save($entity);

        }

        for($i = 8; $i < count($cardIDList) ; $i ++){
            $entity = $gameCards->newEntity();
            $entity->game_id = $gameId;
            $entity->card_id = $cardIDList[$i];
            $entity->owner_ai_id = 0; // Deck
            $entity->is_visible =false;
            $gameCards->save($entity);
        }

        return $this->getCurrentDistributedCards($gameId);
    }

    /**
     * @param $gameId
     * @return CardDistributeStatus|null
     */
    public function getCurrentDistributedCards($gameId)
    {
        $gameCards = TableRegistry::get('game_cards');
        // コントローラやテーブルのメソッド内で
        $query = $gameCards
                    ->find('all')
                    ->where(['game_id ' => $gameId])
                    ->contain(['Cards']);

        if($query->count()==0){
            return null;
        }else{

            if($query->count()<24){
                return new CardDistributeStatus(RESULT_CODE::CARD_DISTRIBUTE_WRONG_CARD_NUM);
            }

            $cardDistributeStatus = new CardDistributeStatus(RESULT_CODE::SUCCESS);
            foreach($query as $row){
                $cardDistributeStatus->addCard($row->owner_ai_id ,$row);
            }

            $cardDistributeStatus->sortCards();
            return $cardDistributeStatus;
        }

    }

}