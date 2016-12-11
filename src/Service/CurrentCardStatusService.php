<?php
/**
 * Todo Resultより独立させる
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 22:18
 */

namespace App\Service;


use App\Constant\ALGO_CONST;
use App\Constant\GAME_CARD;
use App\Model\Entity\GameCard;
use App\Service\GameCardService;
use Cake\ORM\TableRegistry;

class CurrentCardStatusService {

    private $_cardList=[];
    private $_attackCard = null;
    private $_gameAIIds=[];
    private $_numCurrentCard = 0;
    /**
     * @param $gameAIId
     * @param $gameCard
     */
    public function addCard($gameAIId, GameCard $gameCard){
        $this->_numCurrentCard ++;

        if(!in_array($gameAIId,$this->_gameAIIds) && $gameAIId > 0 ){

            $this->_gameAIIds[]=$gameAIId;
        }

        if($this->_numCurrentCard > GAME_CARD::MAX_CARD_NUM){
            throw new \OutOfRangeException("Current game card exceed max num. Game card id" + $gameCard->id);
        }


        //Set Attack Card
        if($gameCard->is_current_attack_card)
        {
            $this->_attackCard = $gameCard;
            return;
        }

        if(!isset($this->_cardList[$gameAIId])){
            $this->_cardList[$gameAIId] = array($gameCard->card->order => $gameCard);
        }else{
            $this->_cardList[$gameAIId][$gameCard->card->order] = $gameCard;
        }




    }

    /**
     * @param $attackAIId
     */
    public function createAttackCard($attackAIId)
    {
        if($this->_attackCard == null){
            $this->_attackCard = array_pop($this->_cardList[GAME_CARD::DECK_CARD_KEY]);
            $this->_attackCard->owner_ai_id = $attackAIId;
            $this->_attackCard->is_current_attack_card = true;

            $gameCards = TableRegistry::get('game_cards');
            $gameCards->save($this->_attackCard,['validate'=>false]);

        }
    }

    /**
     * @return GameCard|null
     */
    public function getCurrentAttackCard()
    {
        return $this->_attackCard;
    }

//    public function sortCards()
//    {
//        foreach ($this->_cardList as $aiId => $cardList){
//            pr($cardList);
//            //Do not sort deck list.
//            if($aiId == 0)  continue;
//            $this->_aiIds[] = $aiId;
////            ksort($cardList);
//            $this->_cardList[$aiId] = $cardList;
//        }
//    }

    public function getCardList()
    {
        return $this->_cardList;
    }

    public function getSourceCard()
    {
        return $this->_sourceCard;
    }

    public function getCardListForDBSave()
    {
        return json_encode(array('CARDS'=>$this->_cardList,'ATTACK_CARD'=>$this->_attackCard));
    }

    public function setCardListFromDbRecord($data)
    {
        $cardData = json_decode($data,true);
        $this->_cardList = $cardData['CARDS'];
        $this->_attackCard = $cardData['ATTACK_CARD'];
    }

    public function getCardListForAiId($aiId)
    {
        return $this->_cardList[$aiId];
    }

    public function getOpponentCardListForGameAIId($gameAIId)
    {
        $tmpCardList =  $this->_cardList;
        unset($tmpCardList[$gameAIId]);
        unset($tmpCardList[GAME_CARD::DECK_CARD_KEY]);
        return array_pop($tmpCardList);
    }

    public function getAllCardArrayForGameAIId($gameAIId)
    {

        //Owners Card
        $myCards = array();
        $opponentCards = array();
        $deckCards = array();

        //自分のカード整理
        $isVisibleForMe = true;
        foreach($this->getCardListForAiId($gameAIId) as $gameCard){
            $myCards[] = $this->getCardArray($gameCard,$isVisibleForMe);
        }


        //相手のカード整理
        foreach($this->getOpponentCardListForGameAIId($gameAIId) as $gameCard){
            $isVisibleForMe = boolval($gameCard->is_visible);
            $opponentCards[] =  $this->getCardArray($gameCard,$isVisibleForMe);
        }


        //カード山の整理
        $isVisibleForMe = false;
        foreach ($this->_cardList[GAME_CARD::DECK_CARD_KEY] as $gameCard) {
            $deckCards[] = $this->getCardArray($gameCard,$isVisibleForMe);
        }

        //攻撃カードの整理
        $isVisibleForMe = ($this->_attackCard->owner_ai_id == $gameAIId);
        $attackCard[] = $this->getCardArray($this->_attackCard,$isVisibleForMe);

        return array("MY_CARD"=>$myCards,'OPPONENT_CARD'=>$opponentCards, 'DECK_CARD'=>$deckCards, 'ATTACK_CARD'=>$attackCard);
    }


    /**
     * @param GameCard $gameCard
     * @param boolean $isVisibleForMe
     * @return array
     */

    private function getCardArray(GameCard $gameCard, $isVisibleForMe){
       return array(
            'ID'=>$gameCard->id,
            'NUMBER'=>($isVisibleForMe)?$gameCard->card->number:ALGO_CONST::UNKNOWN,
            'COLOR'=>$gameCard->card->color,
            'VISIBLE'=>boolval($gameCard->is_visible));
    }

//    public function isValidateAttackCard($gameAIId)
//    {
//        $attackCard = null;
//        if($this->_attackCard == null){
//            $this->_cardList[GAME_CARD::DECK_CARD_KEY][0]
//        }
//        foreach ($this->_cardList[GAME_CARD::DECK_CARD_KEY] as $gameCard) {
//
//        //自分のターンである場合は最初のカードをATTACKカードにする。
//        if($i == 0 && $isMyTurn && $this->_attackCard == null) {
//            $this->_attackCard = $gameCard;
//            $attackCard[] = array(
//                'ID' => $gameCard->id,
//                'NUMBER' => $gameCard->card->number,
//                'COLOR' => $gameCard->card->color,
//                'VISIBLE' => true
//            );
//        }
//    }

    /**
     * @return int
     */
    public function getWinningGameAIId()
    {
        $visibleCounterForAiIds = array();
        foreach ($this->_gameAIIds as $aiId ){
            //Do not sort deck list.
            $visibleCounterForAiIds[$aiId] = 0;
            $allCardIsVisible = true;
            foreach($this->_cardList[$aiId] as $gameCard){
                if(!$gameCard->is_visible){
                    $allCardIsVisible = false;
                }else{
                    $visibleCounterForAiIds[$aiId]++;
                }
            }

            //$aiId is lose
            if($allCardIsVisible){
                $aiIdCopy = $this->_gameAIIds;
                unset($aiIdCopy[$aiId]);
                return (int) array_pop($aiIdCopy);
            }
        }

        //No more cards.
        if(count($this->_cardList[GAME_CARD::DECK_CARD_KEY]) == 0 ){
            return ($visibleCounterForAiIds[$this->_gameAIIds[0]] > $visibleCounterForAiIds[$this->_gameAIIds[1]])?$this->_gameAIIds[0]:$this->_gameAIIds[1];
        }
        return ALGO_CONST::UNKNOWN;
    }

}