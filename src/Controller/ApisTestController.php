<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;
use App\Constant\ACTION_TYPE;
use App\Constant\RESULT_CODE;
use App\Model\Entity\GameTurn;
use App\Model\Vo\InitGameResult;
use App\Model\Vo\Result;
use App\Model\Vo\CheckMatchingResult;
use App\Service\AuthService;
use App\Service\GameCardService;
use App\Service\GameService;
use App\Service\GameTurnService;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use App\Model\Vo\GameTurnHistoriesResult;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ApisTestController extends AppController
{
	private $teamAId = 2;
	private $teamAAuth = "QW1zda@A12a";
	private $teamBId = 3;
	private $teamBAuth = "45@1lWXsiog1";
	public function initialize()
	{
		parent::initialize();
		//$this->loadComponent('RequestHandler');
	}

	public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		$this->Auth->allow();
	}

	/**
	 * Passed Tests.
	 */
	public function testInitGame()
	{
		$initGameResult = (new GameService())->initGame($this->teamAId,"TEAM_A_AI_TYPE_1");
		$this->returnData($initGameResult->getResult());
	}

	/**
	 * Passed Tests
	 */
	public function testCardDistribute()
	{
		$groupId = 1;
		$auth = "QWEXQA12a";
		$gameId = 7;
		$team_a_ai_id = 13;
		$team_b_ai_id = 14;
		$cardService = new GameCardService();
		$result = $cardService->initDistributesCardForGame($gameId,$team_a_ai_id,$team_b_ai_id);
		$isMyTurn = false;
		pr($result->getAllCardListForAiId($team_a_ai_id,$isMyTurn));
		pr($result->getAllCardListForAiId($team_b_ai_id,!$isMyTurn));

	}

	/**
	 * @param $groupId
	 * @param $gameId
	 * @param $gameAIId
	 */
	public function testCheckMatching($gameAIId)
	{
		$groupId = 2;
		$gameId = 1;
		$checkMatchingResult = (new GameService())->checkMatching($groupId,$gameId,$gameAIId);
		$this->returnData($checkMatchingResult->getResult());

	}


	public function testAttack()
	{
		$attackCardId = 9;
		$targetCardId = 1;
		$number = 8;
		$aiId = 2;
		$gameId = 1;
		$turnId = 1;
		$actionType = ACTION_TYPE::ATTACK;
		$gameTurnService = new GameTurnService();
		$result = $gameTurnService->processMyTurn($gameId,$aiId,$turnId,$actionType,$attackCardId,$targetCardId,$number);
		$this->returnData($result->getResult());
	}


	/**
	 * Passed Tests
	 */
	public function testCreateTurn()
	{

		$gameId = 1;
		$currentCount = 1;
		$canStay = false;
		$team_a_ai_id = 1;
		$team_b_ai_id = 2;
		$cardService = new GameCardService();
		$result = $cardService->initDistributesCardForGame($gameId,$team_a_ai_id,$team_b_ai_id);
		$gameTurnService = new GameTurnService();
		$gameTurnService->createTurn($gameId,$team_a_ai_id,$currentCount,$canStay,$result->getCardListForDBSave());
	}


	public function testTurnHistories()
	{
		$gameId = 1;
		$gameTurnService = new GameTurnService();
		$gameTurnHistoriesResult = new GameTurnHistoriesResult(RESULT_CODE::SUCCESS);
		$result = $gameTurnService->getTurnHistory($gameId);
		$gameTurnHistoriesResult->setGameId($gameId);
		$gameTurnHistoriesResult->setHistories($result);
		$this->returnData($gameTurnHistoriesResult->getWellFormedHistories());

	}


	public function testProcessMyTurn($groupId,$gameId,$gameAIId,$turnId,$attackType,$sourceCardId = 0, $targetCardId = 0)
	{

	}

	private function returnData($data)
	{
		$this->set('RESULT',$data);
	}



}
