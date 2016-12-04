<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CodesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CodesTable Test Case
 */
class CodesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CodesTable
     */
    public $Codes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.codes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Codes') ? [] : ['className' => 'App\Model\Table\CodesTable'];
        $this->Codes = TableRegistry::get('Codes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Codes);

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
}
