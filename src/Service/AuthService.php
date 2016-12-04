<?php
/**
 * Created by PhpStorm.
 * User: jung
 * Date: 16/12/04
 * Time: 15:28
 */
namespace App\Service;
use App\Model\Entity\Group;
use App\Model\Table\GroupsTable;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;


class AuthService {
    public function isValidAuth($groupId,$auth)
    {
        $groups = TableRegistry::get('groups');
        $query = $groups->find('all', [
            'conditions'=>['id'=>$groupId,'auth'=>$auth]
        ]);
        $number = $query->count();
        return ($number == 1);

    }
}