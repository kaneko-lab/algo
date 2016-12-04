<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GameCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GameCardsTable Test Case
 */
class GameCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GameCardsTable
     */
    public $GameCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.game_cards',
        'app.games',
        'app.groups',
        'app.game_turns',
        'app.turn_codes',
        'app.users',
        'app.cards',
        'app.owner_ais'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GameCards') ? [] : ['className' => 'App\Model\Table\GameCardsTable'];
        $this->GameCards = TableRegistry::get('GameCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GameCards);

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
