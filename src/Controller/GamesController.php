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

use App\Service\GameCardService;
use App\Service\GameTurnService;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 *
 *
 *
 *
 *
 */
class GamesController extends AppController
{
    public $paginate = [
        'limit'=>25,
        'order'=>["Games.id desc"],
        'contain'=>['AGroups','BGroups','WinAis','CurrentGameTurns','AGroupAis','BGroupAis']
    ];

    public function index(){
        $this->set('games',$this->paginate());
    }

    public function initialize(){
        parent::initialize();
        $this->loadComponent('Paginator');
    }


    public function view($gameId){
        $gameTurns = (new GameTurnService())->getTurnHistoryResultForWatching($gameId);
        $gameCards = (new GameCardService())->getCurrentDistributedCards($gameId);
        $this->set('gameTurns',$gameTurns);
        $this->set('gameCards',$gameCards);

    }
}
