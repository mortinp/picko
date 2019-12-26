<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use App\Model\Entity\SharedTravel;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    
    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow();
        
        $this->viewBuilder()->setLayout('shared_rides');
    }

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            if(in_array($page, ['share-taxi-cuba', 'taxi-vs-viazul'])) $this->viewBuilder()->setLayout('mobirise/empty');
            if($page == 'havana-cienfuegos-trinidad') $this->viewBuilder()->setLayout('mobirise/book_combo');
            if($page == 'faq') $this->viewBuilder()->setLayout('mobirise/empty'); 
            if($page == 'taxi-fleet') $this->viewBuilder()->setLayout('mobirise/taxi_fleet');
            
            if($page == 'taxi-combo') {
                // Sanity check
                $comboStringKey = $subpage;
                if($comboStringKey == null || !array_key_exists($comboStringKey, SharedTravel::$combos)) throw new NotFoundException();

                $this->set('combo', SharedTravel::$combos[$comboStringKey]);
                
                $this->viewBuilder()->setLayout('mobirise/book_combo');
                return $this->render('taxi_combo');
            }
            
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
}
