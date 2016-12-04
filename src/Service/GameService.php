<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 16:36
 */

namespace App\Service;


use App\Constant\DATE_FORMAT;
use App\Constant\MATCHING_TYPE;
use App\Core\Config;
use Symfony\Component\Config\Definition\Exception\Exception;
use Cake\ORM\TableRegistry;

class GameService {


    public function initGame($groupId){
        //Find game id
        //まだ、マッチングされていない Game IDもしくは、新規ゲームID
        $games = TableRegistry::get('games');



        switch(Config::CURRENT_MATCHING_TYPE){
            case MATCHING_TYPE::SAME_TEAM:
                $findQuery = $games->find()->where(
                    ['is_finished'=>false,
                        'OR'=>[
                            ['team_a_group_id'=>$groupId,'team_b_group_id'=>0],
                            ['team_b_group_id'=>$groupId,'team_a_group_id'=>0],

                        ]
                    ]
                );

                break;
            case MATCHING_TYPE::DIFF_TEAM:

                $findQuery = $games->find()->where(
                    ['is_finished'=>false,
                        'OR'=>[
                            ['team_a_group_id <> '=>$groupId,'team_b_group_id'=>0],
                            ['team_b_group_id <> '=>$groupId,'team_a_group_id'=>0],

                        ]
                    ]
                );
                break;

            default :
                throw new Exception("現在のマッチングタイプを確認できません。");


            break;
        }
        $result = $findQuery->first();

        //Create New Game
        if($result == null){
            $currentDate = date(DATE_FORMAT::READABLE_DATE);
            $entity = $games->newEntity();
            $entity->team_a_group_id = $groupId;
            $entity->created = $currentDate;
            $entity->modified = $currentDate;
            $result = $games->save($entity);
            $gameId = $result->id;
        }


        else //Update Game.
        {

            if($result['team_a_group_id'] == 0){
                $updateGroupField = 'team_a_group_id';
            }else{
                $updateGroupField = 'team_b_group_id';
            }
            $gameId = $result->id;

            $updateDate = date(DATE_FORMAT::READABLE_DATE);
            $updateQuery = $games->query();
            $updateQuery
                ->update()
                ->set([$updateGroupField=>$groupId,"modified"=>$updateDate])
                ->where(["id"=>$gameId])
                ->execute();
        }

        return $gameId;
    }
}