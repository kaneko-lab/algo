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
use App\Constant\RESULT_CODE;
use App\Model\Vo\InitGameResult;
use App\Model\Vo\Result;
use App\Model\Vo\CheckMatchingResult;
use App\Service\AuthService;
use App\Service\GameService;
use App\Service\GameTurnService;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ApisController extends AppController
{
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
	 * Displays a view
	 *
	 * @return void|\Cake\Network\Response
	 * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
	 * @throws \Cake\Network\Exception\NotFoundException When the view file could not
	 *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
	 */
	public function test()
	{
		//$jsonData = $this->request->data['query'];
		//$userData = json_decode($jsonData, true);
		$userData = array('user'=>"data");
		$d = json_encode(array("REQ" => "OK", "DATA" => $userData));

		$this->set('result',$d);
	}

	/**
	 * ゲームを初期化する。
	 * 初期化に問題がなければ、Game IDを返す
	 * @param $groupId
	 * @param $auth
	 * @param $gameAICode
	 */
	public function initGame($groupId,$auth,$gameAICode)
	{
		// CHECK AUTH
		if(!$this->checkAuth($groupId,$auth))  return;
		$initGameResult = (new GameService())->initGame($groupId,$gameAICode);
		$this->returnData($initGameResult->getResult());
		return;
	}

	/**
	 * ゲーム対戦相手を待つ
	 * Matchingが成功した場合にはtrueでなければfalseを返す。
	 * @param $groupId
	 * @param $auth
	 * @param $gameId
	 * @param $gameAIId
	 * //TEST URLs
	 * 	- http://algo.local/Apis/checkMatching/1/QWEXQA12a/6/11/.json
	 *  - http://algo.local/Apis/checkMatching/1/QWEXQA12a/6/12/.json
	 *
	 */
	public function checkMatching($groupId,$auth,$gameId,$gameAIId)
	{
		if(!$this->checkAuth($groupId,$auth))  return;
		$checkMatchingResult = (new GameService())->checkMatching($groupId,$gameId,$gameAIId);

		$this->returnData($checkMatchingResult->getResult());

	}

	/**
	 * 現在のTurnの進行状況を確認する。
	 * 自分のTurnか相手のTurnか、相手のTurn終了後の情報や、ゲームが終了したかなどの情報を得ることができる。
	 *
	 * @param $groupId
	 * @param $auth
	 * @param $gameId
	 * @param $gameAIId
	 */
	public function checkCurrentTurn($groupId,$auth,$gameId,$gameAIId)
	{
		if(!$this->checkAuth($groupId,$auth))  return;
		$checkCurrentTurnResult = (new GameTurnService())->checkCurrentTurn($gameId,$gameAIId);
		$this->returnData($checkCurrentTurnResult->getWellFormedData());
	}

	/**
	 * 自分のTurnのアクションを行う。
	 * ATTACK もしくは STAYが可能
	 * STAYは以前のTURNでかなるざATTACKが成功した必要がある。
	 * Turn終了後の情報を得ることができる。
	 */
	/**
	 * @param $groupId
	 * @param $auth
	 * @param $gameId
	 * @param $aiId
	 * @param $turnId
	 * @param $actionType
	 * @param $attackCardId
	 * @param $targetCardId
	 * @param $number
	 */

	//Todo Add attack result to result parameter.

	public function doTurnAction($groupId,$auth,$gameId,$aiId,$turnId,$actionType,$attackCardId,$targetCardId,$number)
	{
		if(!$this->checkAuth($groupId,$auth))  return;
		$gameTurnService = new GameTurnService();
		$result = $gameTurnService->doTurnAction($gameId,$aiId,$turnId,$actionType,$attackCardId,$targetCardId,$number);
		if($result->getCode() == RESULT_CODE::SUCCESS)
			$this->returnData($result->getWellFormedData());
		else
			$this->returnData($result->getResult());
	}

	private function checkAuth($groupId,$auth)
	{
		//Failed
		if(!(new AuthService())->isValidAuth($groupId,$auth)){
			$result = new Result(RESULT_CODE::AUTH_FAILED);
			$result = $result->getResult();
			$this->returnData($result);
			return false;
		}
		return true;
	}

	/**
	 * @param $data
	 */
	private function returnData($data)
	{
		$this->set('RESULT',$data);
	}
}
