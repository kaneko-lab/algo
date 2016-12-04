<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Game Entity
 *
 * @property int $id
 * @property int $team_a_group_id
 * @property int $team_b_group_id
 * @property int $win_group_id
 * @property bool $is_finished
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $finished_datetime
 *
 * @property \App\Model\Entity\TeamAGroup $team_a_group
 * @property \App\Model\Entity\TeamBGroup $team_b_group
 * @property \App\Model\Entity\WinGroup $win_group
 * @property \App\Model\Entity\GameTurn[] $game_turns
 */
class Game extends Entity
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
        'id' => false
    ];
}
