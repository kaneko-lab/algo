<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 18:02
 */

namespace App\Constant;


class RESULT_CODE extends Enum{
    const SUCCESS = 1000;
    const AUTH_FAILED = 9999;
    const MATCHING_INVALID_GROUP_ID = 2002;
    const MATCHING_INVALID_AI_ID = 2003;
    const CARD_DISTRIBUTE_WRONG_CARD_NUM = 3002;


}