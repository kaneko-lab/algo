<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/05
 * Time: 18:28
 */

namespace App\Model\Vo;


use App\Constant\GAME_CARD;
use Cake\I18n\Time;
class GameTurnHistoriesResult extends Result{
    private $_gameId;
    private $_histories = [];
    public function setGameId($gameId)
    {

    }

    public function setHistories($histories)
    {
        $this->_histories = $histories;
    }


    public function getWellFormedHistories()
    {
        $data = [];
        foreach($this->_histories as $history){

            $atkCardInfo=[];
            if(!empty($history->attack_game_card)){
                $atkCardInfo =[
                    'GAME_CARD_ID'=>$history->attack_game_card->id,
                    'CARD_ID'=>$history->attack_game_card->card_id,
                    'NUMBER' =>GAME_CARD::getNumber($history->attack_game_card->id),
                    'COLOR'  =>GAME_CARD::getColor($history->attack_game_card->id),
                ];
            }

            $tgtCardInfo=[];
            if(!empty($history->target_game_card)){
                $tgtCardInfo=[
                    'GAME_CARD_ID'=>$history->target_game_card->id,
                    'CARD_ID'=>$history->target_game_card->card_id,
                    'NUMBER' =>GAME_CARD::getNumber($history->target_game_card->id),
                    'COLOR'  =>GAME_CARD::getColor($history->target_game_card->id),
                ];
            }

            $turnEnded  = 0;
            if(!empty($history->turn_ended))
                $turnEnded = $history->turn_ended->i18nFormat(Time::UNIX_TIMESTAMP_FORMAT);
            $data[$history->current_count] = array(
                'TURN_ID'=>$history->id,
                'AI_ID'=>$history->game_ai_id,
                'ACTION_CODE'=>$history->turn_action_code,
                'ATK_CARD'=>$atkCardInfo,
                'TGT_CARD'=>$tgtCardInfo,
                'CAN_STAY'=>boolval($history->can_stay),
                'IS_STAY'=>boolval($history->is_stay),
                'IS_SUCCESS_ATK'=>boolval($history->is_success_attack),
                'COUNT'=>$history->current_count,
                'IS_FINISHED'=>boolval($history->is_finished),
                'TURN_STARTED'=>$history->created->i18nFormat(Time::UNIX_TIMESTAMP_FORMAT),
                'TURN_ENDED'=>$turnEnded
            );
        }
        return $data;
    }


}