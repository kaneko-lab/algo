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
 * @property \Cake\ORM\Association\BelongsTo $Groups
 * @property \Cake\ORM\Association\BelongsTo $TurnCodes
 *
 * @method \App\Model\Entity\GameTurn get($primaryKey, $options = [])
 * @method \App\Model\Entity\GameTurn newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GameTurn[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GameTurn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GameTurn findOrCreate($search, callable $callback = null)
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
        $this->primaryKey(['id', 'game_id', 'group_id', 'turn_code_id']);

        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TurnCodes', [
            'foreignKey' => 'turn_code_id',
            'joinType' => 'INNER'
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
            ->integer('ai_uid')
            ->allowEmpty('ai_uid');

        $validator
            ->requirePresence('turn_result', 'create')
            ->notEmpty('turn_result');

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
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        $rules->add($rules->existsIn(['turn_code_id'], 'TurnCodes'));

        return $rules;
    }
}
