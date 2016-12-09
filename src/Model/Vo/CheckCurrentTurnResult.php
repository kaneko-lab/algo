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
use App\Service\CurrentCardStatusService;
class CheckCurrentTurnResult extends Result
{
    private $_gameEntity;

    /**
     * @var GameTurnHistoriesResult
     */
    private $_gameTurnHistoriesResult;
    private $_isProcessing;
    private $_gameAiId;
    private $_winnerGameAIId = -1 ;
    private $_canStayThisTurn = false;
    /**
     * @var CurrentCardStatusService
     */
    private $_currentCardStatus;

    public function setCanStayThisTurn($canStay)
    {
        $this->_canStayThisTurn = $canStay;
    }

    public function setWinnerGameAIId($winnerGameAIId)
    {
        $this->_winnerGameAIId = $winnerGameAIId;
    }

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

    public function setCurrentCardStatus(CurrentCardStatusService $currentCardStatus)
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
                    JSON_KEY::TURN_WINNER_INFO=>($this->_winnerGameAIId == 0)?GAME_CARD::UNKNOWN:$this->_winnerGameAIId,
                    JSON_KEY::TURN_INFO=>[JSON_KEY::ID=>$this->_gameEntity->current_turn_id,JSON_KEY::TURN_IS_MINE=>$isMyTurn,JSON_KEY::TURN_CAN_STAY=>$this->_canStayThisTurn],
                    JSON_KEY::CARD_DATA=>$this->_currentCardStatus->getAllCardArrayForGameAIId($this->_gameAiId,$isMyTurn),
                    JSON_KEY::TURN_HISTORIES=>$this->_gameTurnHistoriesResult->getWellFormedHistories()
                ]
        ];
        return $data;
    }
}