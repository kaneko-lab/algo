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
use App\Model\Vo\InitGameResult;
use App\Service\AuthService;
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
		$jsonData = $this->request->data['query'];
		$userData = json_decode($jsonData, true);
		$data = array("REQ" => "OK", "DATA" => $userData);
		echo json_encode($data);
		exit;
	}

	/**
	 * ゲームを初期化する。
	 * 初期化に問題がなければ、Game IDを返す
	 * @param $groupId
	 * @param $auth
	 */
	public function initGame($groupId,$auth)
	{
		//SUCCESS
		if((new AuthService())->isValidAuth($groupId,$auth)){
			$result = new InitGameResult("001");
			echo $result->getJsonResult();
		}else{
			$result = new InitGameResult("002");
			echo $result->getJsonResult();
		}
		exit;
	}

	/**
	 * ゲーム対戦相手を待つ
	 * Matchingが成功した場合にはtrueでなければfalseを返す。
	 * @param $groupId
	 * @param $auth
	 * @param $gameId
	 */
	public function waitMatching($groupId,$auth,$gameId)
	{

	}

	/**
	 * 現在のTurnの進行状況を確認する。
	 * 自分のTurnか相手のTurnか、相手のTurn終了後の情報や、ゲームが終了したかなどの情報を得ることができる。
	 */
	public function checkCurrentTurn()
	{

	}

	/**
	 * 自分のTurnのアクションを行う。
	 * ATTACK もしくは STAYが可能
	 * STAYは以前のTURNでかなるざATTACKが成功した必要がある。
	 * Turn終了後の情報を得ることができる。
	 */
	public function turnAction()
	{

	}

}
