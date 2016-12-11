<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/05
 * Time: 10:25
 */

namespace App\Model\Vo;


use App\Constant\ALGO_CONST;
use App\Constant\GAME_CARD;

class CreateTurnResult extends Result {
    private $_turnData;
    private $_currentTurnId;



    public function setTurnData($turnId, $turnData)
    {
        if($turnId < 1)
            $this->_currentTurnId = ALGO_CONST::UNKNOWN;
        else
            $this->_currentTurnId = $turnId;

        $this->_turnData = $turnData;
    }

    public function getTurnId()
    {
        return $this->_currentTurnId;
    }
}