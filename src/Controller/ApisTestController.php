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
	private $teamAId = 127;
	private $teamAAuth = "XIDKE12sli11A";
	private $teamBId = 231;
	private $teamBAuth = "eXIaseig1@1sa";
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
	 *
	 * Passed Tests.
	 *
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
		pr($result->getAllCardArrayForGameAIId($team_a_ai_id));
		pr($result->getAllCardArrayForGameAIId($team_b_ai_id));

	}


	/**
	 * @param $gameAIId
	 */
	public function testCheckMatching($gameAIId)
	{
		$groupId = 127;
		$gameId = 1520;
		$checkMatchingResult = (new GameService())->checkMatching($groupId,$gameId,$gameAIId);
		$this->returnData($checkMatchingResult->getResult());

	}




	//	public function doTurnAction($groupId = null, $auth = null, $gameId = null, $gameAIId = null, $turnId = null, $actionType = null, $attackCardId = null, $targetCardId = null, $number = null)

    // Group ID = 234
    // auth = xxxeXIaseig1@1sa
    // gameId = 3330
    // $gameAIId = 6654
    // $turnId = 33249
    // $actionType = STAY
    // $attackCardId = -1
    // $targetCardId = -1
    // $number = -1

    //http://algo.kaneko-lab.net/Apis/doTurnAction/234/xxxeXIaseig1@1sa/3330/6654/33249/STAY/-1/-1/-1.json
	public function testDoTurnAction()
	{
		$attackCardId = 16;
		$targetCardId = 7;
		$number = 0;
		$gameAIId = 1;
		$gameId = 1;
		$turnId = 1;
		$actionType = ACTION_TYPE::ATTACK;
		$gameTurnService = new GameTurnService();
		$result = $gameTurnService->doTurnAction($gameId,$gameAIId,$turnId,$actionType,$attackCardId,$targetCardId,$number);
		if($result->getCode() == RESULT_CODE::SUCCESS)
			$this->returnData($result->getWellFormedData());
		else
			$this->returnData($result->getResult());
	}


	public function testCheckCurrentTurn()
	{
		$gameId = 1;
		$gameAIId = 2;
		$checkCurrentTurnResult = (new GameTurnService())->checkCurrentTurn($gameId,$gameAIId);
		$this->returnData($checkCurrentTurnResult->getWellFormedData());
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
		$gameAiId = 1;
		$gameTurnService = new GameTurnService();
		$gameTurnHistoriesResult = new GameTurnHistoriesResult(RESULT_CODE::SUCCESS);
		$result = $gameTurnService->getTurnHistoryResult($gameId,$gameAiId);
		$gameTurnHistoriesResult->setGameId($gameId);
		$gameTurnHistoriesResult->setHistories($result);
		$this->returnData($gameTurnHistoriesResult->getWellFormedHistories());

	}

	private function returnData($data)
	{
		$this->set('RESULT',$data);
	}



}
