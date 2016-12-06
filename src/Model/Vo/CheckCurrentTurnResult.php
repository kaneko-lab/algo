<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/06
 * Time: 10:44
 */

namespace App\Model\Vo;


use App\Constant\GAME_CARD;
use App\Constant\JSON_KEY;

class CheckCurrentTurnResult extends Result
{
    private $_gameEntity;

    /**
     * @var GameTurnHistoriesResult
     */
    private $_gameTurnHistoriesResult;
    private $_isProcessing;
    private $_gameAiId;
    /**
     * @var CurrentCardStatus
     */
    private $_currentCardStatus;

    public function setGameEntity($gameEntity)
    {
        $this->_gameEntity = $gameEntity;
    }

    public function setGameTurnHistoriesResult(GameTurnHistoriesResult $gameTurnHistoriesResult)
    {
        $this->_gameTurnHistoriesResult = $gameTurnHistoriesResult;
    }


    public function setIsProcessing($isProcessing)
    {
        $this->_isProcessing = $isProcessing;
    }

    public function setGameAiId($gameAiId)
    {
        $this->_gameAiId = $gameAiId;
    }

    public function setCurrentCardStatus(CurrentCardStatus $currentCardStatus)
    {
        $this->_currentCardStatus = $currentCardStatus;
    }

    public function getWellFormedData()
    {
        $isMatched = ($this->_gameEntity->team_a_ai_id > 0 && $this->_gameEntity->team_b_ai_id >0 );
        $isMyTurn = ($this->_gameEntity->current_ai_id == $this->_gameAiId);
        $data =[
            JSON_KEY::RESULT_CODE => $this->_resultCode,
            JSON_KEY::RESULT_DESC => $this->_resultDesc,
            JSON_KEY::RESULT_DATA =>
                [
                    JSON_KEY::IS_MATCHED=>$isMatched,
                    JSON_KEY::TURN_INFO=>[JSON_KEY::ID=>$this->_gameEntity->current_turn_id,JSON_KEY::TURN_IS_MINE=>$isMyTurn],
                    JSON_KEY::CARD_DATA=>$this->_currentCardStatus->getAllCardListForAiId($this->_gameAiId,$isMyTurn),
                    JSON_KEY::TURN_HISTORIES=>$this->_gameTurnHistoriesResult->getWellFormedHistories()
                ]
        ];
        return $data;
    }
}