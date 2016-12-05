<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/05
 * Time: 10:25
 */

namespace App\Model\Vo;


class CreateTurnResult extends Result {
    private $_turnData;
    private $_currentTurnId;



    public function setTurnData($turnId, $turnData)
    {
        $this->_currentTurnId = $turnId;
        $this->_turnData = $turnData;
    }

    public function getTurnId()
    {
        return $this->_currentTurnId;
    }
}