<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 22:05
 */

namespace App\Constant;


class GAME_CARD extends Enum{

    //Card ID LIST FOR DATABASE
    const WHITE_0 = 1;
    const WHITE_1 = 2;
    const WHITE_2 = 3;
    const WHITE_3 = 4;
    const WHITE_4 = 5;
    const WHITE_5 = 6;
    const WHITE_6 = 7;
    const WHITE_7 = 8;
    const WHITE_8 = 9;
    const WHITE_9 = 10;
    const WHITE_10 = 11;
    const WHITE_11 = 12;
    const BLACK_0 = 13;
    const BLACK_1 = 14;
    const BLACK_2 = 15;
    const BLACK_3 = 16;
    const BLACK_4 = 17;
    const BLACK_5 = 18;
    const BLACK_6 = 19;
    const BLACK_7 = 20;
    const BLACK_8 = 21;
    const BLACK_9 = 22;
    const BLACK_10 = 23;
    const BLACK_11 = 24;
    const UNKNOWN = -1;

    public static function getNumber($cardId){
        $number = $cardId - 1 ;
        if($number >= 12)
            $number -= 12;
        return $number;
    }


    public static function getColor($cardId){
        if($cardId <= 12)
            return "WHITE";
        else
            return "BLACK";
    }
}