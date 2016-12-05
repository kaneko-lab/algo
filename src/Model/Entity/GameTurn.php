<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GameTurn Entity
 *
 * @property int $id
 * @property int $game_id
 * @property int $game_ai_id
 * @property int $current_count
 * @property string $turn_action_code
 * @property int $attack_game_card_id
 * @property int $target_game_card_id
 * @property bool $can_stay
 * @property bool $is_stay
 * @property bool $is_success_attack
 * @property string $turn_before
 * @property string $turn_result
 * @property bool $is_finished
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $turn_ended
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
        'id' => false
    ];
}
