<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 22:18
 */

namespace App\Model\Vo;


class CardDistributeStatus extends Result{
    private $_cardList=[];

    public function addCard($aiId, $gameCard){
        if(!isset($this->_cardList[$aiId])){
            $this->_cardList[$aiId] = array($gameCard->card->order => $gameCard);
        }else{
            $this->_cardList[$aiId][$gameCard->card->order] = $gameCard;
        }
    }

    public function sortCards()
    {
        foreach ($this->_cardList as $aiId => $cardList){
            ksort($cardList);
            $this->_cardList[$aiId] = $cardList;
        }
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

    public function getAllCardListForAiId($aiId)
    {
        //Owners Card
        $myCards = array();
        $opponentCards = array();

        foreach($this->getCardListForAiId($aiId) as $gameCard){
            $myCards[$gameCard->id] =
                array(
                    'NUMBER'=>$gameCard->card->number,
                    'COLOR'=>$gameCard->card->color,
                    'VISIBLE'=>true
                    );
        }

        foreach($this->getOpponentCardListForAiId($aiId) as $gameCard){
            $number = ($gameCard->is_visible)?$gameCard->card->number:-1;

            $opponentCards[$gameCard->id] =
                array(
                    'NUMBER'=>$number,
                    'COLOR'=>$gameCard->card->color,
                    'VISIBLE'=>($gameCard->is_visible == true)
                );
        }

        return array("MY_CARD"=>$myCards,'OPPONENT_CARD'=>$opponentCards);
    }

}