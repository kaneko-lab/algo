<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 15:51
 */

namespace App\Model\Vo;


class InitGameResult extends Result{
    private $_gameAIId;
    private $_gameId;

    public function setGameAIId($id)
    {
        $this->_gameAIId = $id;
    }

    public function setGameId($id)
    {
        $this->_gameId = $id;
    }

    public function getResult()
    {
        return (
                [
                    'CODE' => $this->_resultCode,
                    'DESC' => $this->_resultDesc,
                    'DATA' => ['GAME_ID'=>$this->_gameId,'GAME_AI_ID'=>$this->_gameAIId]]);
    }
}