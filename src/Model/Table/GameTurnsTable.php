<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GameTurns Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Games
 * @property \Cake\ORM\Association\BelongsTo $GameAis
 * @property \Cake\ORM\Association\BelongsTo $SourceGameCards
 * @property \Cake\ORM\Association\BelongsTo $TargetGameCards
 *
 * @method \App\Model\Entity\GameTurn get($primaryKey, $options = [])
 * @method \App\Model\Entity\GameTurn newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GameTurn[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GameTurn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GameTurnsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('game_turns');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('GameAis', [
            'foreignKey' => 'game_ai_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('AttackGameCards', [
            'className'=>'GameCards',
            'foreignKey' => 'attack_game_card_id'
        ]);
        $this->belongsTo('TargetGameCards', [
            'className'=>'GameCards',
            'foreignKey' => 'target_game_card_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('current_count')
            ->allowEmpty('current_count');

        $validator
            ->allowEmpty('turn_action_code');

        $validator
            ->boolean('can_stay')
            ->allowEmpty('can_stay');

        $validator
            ->boolean('is_stay')
            ->allowEmpty('is_stay');

        $validator
            ->boolean('is_success_attack')
            ->allowEmpty('is_success_attack');

        $validator
            ->allowEmpty('turn_before');

        $validator
            ->allowEmpty('turn_result');

        $validator
            ->boolean('is_finished')
            ->allowEmpty('is_finished');

        $validator
            ->dateTime('turn_ended')
            ->allowEmpty('turn_ended');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['game_id'], 'Games'));
        $rules->add($rules->existsIn(['game_ai_id'], 'GameAis'));
        $rules->add($rules->existsIn(['attack_game_card_id'], 'AttackGameCards'));
        $rules->add($rules->existsIn(['target_game_card_id'], 'TargetGameCards'));

        return $rules;
    }
}
