<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 15:51
 */

namespace App\Model\Vo;
use App\COnstant\JSON_KEY;


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
                    JSON_KEY::RESULT_CODE  => $this->_resultCode,
                    JSON_KEY::RESULT_DESC => $this->_resultDesc,
                    JSON_KEY::RESULT_DATA => ['GAME_ID'=>$this->_gameId,'GAME_AI_ID'=>$this->_gameAIId]]);
    }
}