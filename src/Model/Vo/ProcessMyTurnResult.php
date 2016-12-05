<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/05
 * Time: 12:22
 */

namespace App\Model\Vo;


class ProcessMyTurnResult extends Result{
    private $_isGameOver = false;
    private $_winnerAiId = 0;
    /**
     * @var CurrentCardStatus
     */
    private $_currentCardStatus = null;
    public function setWinnerAiId($winnerAiId)
    {
        $this->_winnerAiId = $winnerAiId;
        $this->_isGameOver = true;
    }

    public function setCurrentCardStatus(CurrentCardStatus $currentCardStatus)
    {
        $this->_currentCardStatus = $currentCardStatus;
    }

    public function setTurnHistories(){

    }
}