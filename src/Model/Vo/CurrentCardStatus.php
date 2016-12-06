<?php
/**
 * Todo Resultより独立させる
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 22:18
 */

namespace App\Model\Vo;


use App\Constant\GAME_CARD;

class CurrentCardStatus extends Result{
    private $_cardList=[];
    private $_attackCard = null;
    private $_aiIds=[];
    public function addCard($aiId, $gameCard){
        if($gameCard->is_current_attack_card)
        {
            $this->_attackCard = $gameCard;
            return;
        }

        if(!isset($this->_cardList[$aiId])){
            $this->_cardList[$aiId] = array($gameCard->card->order => $gameCard);
        }else{
            $this->_cardList[$aiId][$gameCard->card->order] = $gameCard;
        }
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

    public function getOpponentCardListForAiId($aiId)
    {
        $tmpCardList =  $this->_cardList;
        unset($tmpCardList[$aiId]);
        unset($tmpCardList[0]);
        return array_pop($tmpCardList);
    }

    public function getAllCardListForAiId($aiId,$isMyTurn)
    {

        //Owners Card
        $myCards = array();
        $opponentCards = array();
        $deckCards = array();

        //自分のカード整理
        foreach($this->getCardListForAiId($aiId) as $gameCard){
            $myCards[] =
                array(
                    'ID'=>$gameCard->id,
                    'NUMBER'=>$gameCard->card->number,
                    'COLOR'=>$gameCard->card->color,
                    'VISIBLE'=>true
                    );
        }


        //相手のカード整理
        foreach($this->getOpponentCardListForAiId($aiId) as $gameCard){
            $number = ($gameCard->is_visible)?$gameCard->card->number:GAME_CARD::UNKNOWN;
            $gameCardId = $gameCard->id;
            $opponentCards[] =
                array(
                    'ID'=>$gameCardId,
                    'NUMBER'=>$number,
                    'COLOR'=>$gameCard->card->color,
                    'VISIBLE'=>boolval($gameCard->is_visible)
                );
        }

        //カード山の整理
        $i = 0;

        $attackCard = null;
        if($this->_attackCard != null){
            $attackCard[] =  array(
                'ID'=>$this->_attackCard->id,
                'NUMBER'=>$this->_attackCard->card->number,
                'COLOR'=>$this->_attackCard->card->color,
                'VISIBLE'=>true
            );
        }


        foreach ($this->_cardList[0] as $gameCard) {
            //自分のターンである場合は最初のカードをATTACKカードにする。
            if($i == 0 && $isMyTurn && $this->_attackCard == null){
                $this->_attackCard = $gameCard;
                $attackCard[] =  array(
                    'ID'=>$gameCard->id,
                    'NUMBER'=>$gameCard->card->number,
                    'COLOR'=>$gameCard->card->color,
                    'VISIBLE'=>true
                );
            }else{
                $deckCards[] =
                    array(
                        'ID'=>$gameCard->id,
                        'NUMBER'=>GAME_CARD::UNKNOWN,
                        'COLOR'=>$gameCard->card->color,
                        'VISIBLE'=>false
                    );
            }
            $i++;
        }


        return array("MY_CARD"=>$myCards,'OPPONENT_CARD'=>$opponentCards, 'DECK_CARD'=>$deckCards, 'ATTACK_CARD'=>$attackCard);
    }

    /**
     * @return int
     */
    public function getWinningAiId()
    {
        $visibleCounterForAiIds = array();
        foreach ($this->_aiIds as $aiId ){
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
                $aiIdCopy = $this->_aiIds;
                unset($aiIdCopy[$aiId]);
                return (int) array_pop($aiIdCopy);
            }
        }

        //No more cards.
        if(count($this->_cardList[0]) == 0 ){
            return ($visibleCounterForAiIds[$this->_aiIds[0]] > $visibleCounterForAiIds[$this->_aiIds[1]])?$this->_aiIds[0]:$this->_aiIds[1];
        }
        return 0;
    }

}