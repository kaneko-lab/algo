<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GameTurn Entity
 *
 * @property int $id
 * @property int $game_id
 * @property int $group_id
 * @property int $turn_code_id
 * @property int $ai_uid
 * @property string $turn_result
 *
 * @property \App\Model\Entity\Game $game
 * @property \App\Model\Entity\Group $group
 * @property \App\Model\Entity\TurnCode $turn_code
 */
class GameTurn extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'game_id' => false,
        'group_id' => false,
        'turn_code_id' => false
    ];
}
