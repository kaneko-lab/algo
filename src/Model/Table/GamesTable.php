<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Games Model
 *
 * @property \Cake\ORM\Association\BelongsTo $TeamAGroups
 * @property \Cake\ORM\Association\BelongsTo $TeamBGroups
 * @property \Cake\ORM\Association\BelongsTo $WinGroups
 * @property \Cake\ORM\Association\HasMany $GameTurns
 *
 * @method \App\Model\Entity\Game get($primaryKey, $options = [])
 * @method \App\Model\Entity\Game newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Game[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Game|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Game patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Game[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Game findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GamesTable extends Table
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

        $this->table('games');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('TeamAGroups', [
            'foreignKey' => 'team_a_group_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TeamBGroups', [
            'foreignKey' => 'team_b_group_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('WinGroups', [
            'foreignKey' => 'win_group_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('GameTurns', [
            'foreignKey' => 'game_id'
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
            ->boolean('is_finished')
            ->requirePresence('is_finished', 'create')
            ->notEmpty('is_finished');

        $validator
            ->dateTime('finished_datetime')
            ->allowEmpty('finished_datetime');

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
        $rules->add($rules->existsIn(['team_a_group_id'], 'TeamAGroups'));
        $rules->add($rules->existsIn(['team_b_group_id'], 'TeamBGroups'));
        $rules->add($rules->existsIn(['win_group_id'], 'WinGroups'));

        return $rules;
    }
}