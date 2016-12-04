<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GameTurnsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GameTurnsTable Test Case
 */
class GameTurnsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GameTurnsTable
     */
    public $GameTurns;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.game_turns',
        'app.games',
        'app.groups',
        'app.turn_codes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GameTurns') ? [] : ['className' => 'App\Model\Table\GameTurnsTable'];
        $this->GameTurns = TableRegistry::get('GameTurns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GameTurns);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
