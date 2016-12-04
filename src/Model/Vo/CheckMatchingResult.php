<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 18:05
 */

namespace App\Model\Vo;

use App\Constant\RESULT_DESC;
class CheckMatchingResult extends Result{
    private $_isFinishedMatching =false;
    private $_isMyTurn = false;
    private $_myAiId = 0;

    /**
     * @var CardDistributeStatus
     */
    private $_cardDistributeStatus;


    public function __construct($resultCode,$myAiId)
    {
        $this->_resultCode = $resultCode;
        $this->_resultDesc = RESULT_DESC::get($resultCode);
        $this->_myAiId = $myAiId;
    }

    public function setMyAiId($myAiId)
    {
        $this->_myAiId = $myAiId;
    }

    public function setCardLists(CardDistributeStatus $cardDistributeStatus){
            $this->_cardDistributeStatus = $cardDistributeStatus;
    }

    public function setIsFinishedMatching($isFinished)
    {
        return $this->_isFinishedMatching = $isFinished;
    }

    public function setIsMyTurn($isMyTurn)
    {
        return $this->_isMyTurn = $isMyTurn;
    }


    public function getResult()
    {
        $cardData  = null;
        if($this->_cardDistributeStatus != null){
            $cardData   = $this->_cardDistributeStatus->getAllCardListForAiId($this->_myAiId);
        }
        return (
        [
            'CODE' => $this->_resultCode,
            'DESC' => $this->_resultDesc,
            'DATA' =>
                ['MATCH'=>$this->_isFinishedMatching,
                 'MY_TURN'=>$this->_isMyTurn,
                 'CARD_DATA'=>$cardData]]);
    }
}