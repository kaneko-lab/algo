<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 21:24
 */

namespace App\Model\Vo;


class Card {
    private $_id;
    private $_number;
    private $_color;
    private $_owner;
    private $_isVisible;

    public function __constructor($id,$number,$color,$owner,$isVisible){
        $this->_id = $id;
        $this->_number = $number;
        $this->_owner = $owner;
        $this->_color = $color;
        $this->_isVisible = $isVisible;
    }

}