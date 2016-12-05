<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GameAis Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Games
 * @property \Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \App\Model\Entity\GameAi get($primaryKey, $options = [])
 * @method \App\Model\Entity\GameAi newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GameAi[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GameAi|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GameAi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GameAi[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GameAi findOrCreate($search, callable $callback = null)
 */
class GameAisTable extends Table
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

        $this->table('game_ais');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
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
            ->allowEmpty('name');

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

        return $rules;
    }
}
