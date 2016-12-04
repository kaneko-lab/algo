<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GameAisTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GameAisTable Test Case
 */
class GameAisTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GameAisTable
     */
    public $GameAis;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.game_ais',
        'app.games',
        'app.groups',
        'app.game_turns',
        'app.turn_codes',
        'app.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GameAis') ? [] : ['className' => 'App\Model\Table\GameAisTable'];
        $this->GameAis = TableRegistry::get('GameAis', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GameAis);

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
