<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 15:47
 */
namespace App\Model\Vo;

class Result {
    private $_resultCode;
    public function __construct($resultCode){
        $this->_resultCode = $resultCode;
    }

    public function getJsonResult(){
        return json_encode(['CODE'=>$this->_resultCode]);
    }
}