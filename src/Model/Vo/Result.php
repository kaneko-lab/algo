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
use App\COnstant\JSON_KEY;

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
                JSON_KEY::RESULT_CODE => $this->_resultCode,
                JSON_KEY::RESULT_DESC => $this->_resultDesc,
                JSON_KEY::RESULT_DATA => null]);
    }
}