<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 18:05
 */

namespace App\Model\Vo;

use App\Constant\JSON_KEY;
use App\Constant\RESULT_DESC;
use App\Service\CurrentCardStatusService;
class CheckMatchingResult extends Result{
    private $_isFinishedMatching =false;
    private $_isMyTurn = false;
    private $_myAiId = 0;
    private $_turnId = 0;

    /**
     * @var CurrentCardStatusService
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

    public function setCardLists(CurrentCardStatusService $cardDistributeStatus){
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

    public function setTurnId($turnId)
    {
        $this->_turnId = $turnId;
    }


    public function getResult()
    {
        $cardData  = null;
        if($this->_cardDistributeStatus != null){
            $cardData   = $this->_cardDistributeStatus->getAllCardArrayForGameAIId($this->_myAiId,$this->_isMyTurn);
        }
        return (
        [
            JSON_KEY::RESULT_CODE => $this->_resultCode,
            JSON_KEY::RESULT_DESC => $this->_resultDesc,
            JSON_KEY::RESULT_DATA =>
                [JSON_KEY::IS_MATCHED=>$this->_isFinishedMatching,
                 JSON_KEY::TURN_INFO=>[JSON_KEY::ID=>$this->_turnId,JSON_KEY::TURN_IS_MINE=>$this->_isMyTurn],
                 JSON_KEY::CARD_DATA=>$cardData,
                ]]);
    }
}