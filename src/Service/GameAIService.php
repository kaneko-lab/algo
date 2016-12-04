<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 20:25
 */

namespace App\Service;

use Cake\ORM\TableRegistry;

class GameAIService {
    public function getNewAI($groupId,$gameId,$gameAICode){
        $gameAIs = TableRegistry::get('game_ais');
        $entity = $gameAIs->newEntity();
        $entity->group_id = $groupId;
        $entity->game_id = $gameId;
        $entity->name = $gameAICode;
        $record = $gameAIs->save($entity);
        return $record->id;
    }
}