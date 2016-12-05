<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 18:11
 */

namespace App\Constant;


class RESULT_DESC {
    public static function get($RESULT_CODE)
    {
        switch($RESULT_CODE){
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";

            case RESULT_CODE::AUTH_FAILED: return "Check you group id and auth key.";

            case RESULT_CODE::MATCHING_INVALID_GROUP_ID: return "Invalid group id.";

            case RESULT_CODE::MATCHING_INVALID_AI_ID: return "Invalid AI id.";

            case RESULT_CODE::PROCESS_MY_TURN_FAILED_PREVIOUS_TURN_LOCK: return "Previous turn process still progress.";

            case RESULT_CODE::PROCESS_MY_TURN_FAILED_NOT_MY_TURN: return "Turn is not mine.";

            case RESULT_CODE::PROCESS_MY_TURN_FAILED_ALREADY_FINISHED: return "Turn already finished .";

            case RESULT_CODE::PROCESS_MY_TURN_FAILED_CANNOT_STAY: return "This turn you can't choice stay.";

            /*
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            case RESULT_CODE::SUCCESS: return "Successfully finished your request.";
            */
            return "UNKNOWN CODE FOR DESCRIPTION " . $RESULT_CODE;
        }
    }
}