<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GameCard Entity
 *
 * @property int $id
 * @property int $game_id
 * @property int $card_id
 * @property int $owner_ai_id
 * @property int $is_visible
 *
 * @property \App\Model\Entity\Game $game
 * @property \App\Model\Entity\Card $card
 * @property \App\Model\Entity\OwnerAi $owner_ai
 */
class GameCard extends Entity
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
