<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 15:47
 */
namespace App\Model\Vo;
use App\Constant\RESULT_CODE;
use App\Constant\RESULT_DESC;

class Result {
    protected $_resultCode;
    protected $_resultDesc;

    public function __construct($resultCode){
        $this->_resultCode = $resultCode;
        $this->_resultDesc = RESULT_DESC::get($resultCode);
    }


    function getCode()
    {
        return $this->_resultCode;
    }

    function getResult()
    {
        return (
            [
            'CODE' => $this->_resultCode,
            'DESC' => $this->_resultDesc,
            'DATA' => null]);
    }
}