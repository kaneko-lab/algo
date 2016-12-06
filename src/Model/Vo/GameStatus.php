<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/06
 * Time: 10:08
 */

namespace App\Model\Vo;


class GameStatus {

    private $_gameEntity;
    private $_currentTurnId;
    private $_isFinished;
    /**
     * @param $gameEntity
     */
    public function setGameEntity($gameEntity){
        $this->_gameEntity = $gameEntity;
    }

//    public function setIsMyTurn($isMyTurn)
//    {
//        return $this->_isMyTurn = $isMyTurn;
//    }
//
//    public function setTurnId($turnId)
//    {
//        $this->_turnId = $turnId;
//    }

    public function getGameStatusForGameAiId($gameAiId){

    }
}