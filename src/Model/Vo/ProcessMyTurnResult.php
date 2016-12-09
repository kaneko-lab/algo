<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/05
 * Time: 12:22
 */

namespace App\Model\Vo;

use App\Service\CurrentCardStatusService;

@Deprecated;
class ProcessMyTurnResult extends Result{
    private $_isGameOver = false;
    private $_winnerAiId = 0;
    /**
     * @var CurrentCardStatusService
     */
    private $_currentCardStatus = null;

    private $_gameTurnHistoriesResult = null;




    public function setWinnerGameAIId($winnerAiId)
    {
        $this->_winnerAiId = $winnerAiId;
        $this->_isGameOver = true;
    }

    public function setCurrentCardStatus(CurrentCardStatusService $currentCardStatus)
    {
        $this->_currentCardStatus = $currentCardStatus;
    }

    public function setTurnHistories(GameTurnHistoriesResult $gameTurnHistoriesResult){

    }
}