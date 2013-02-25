<?php
App::uses('AppController', 'Controller');
/**
 * Societes Controller
 *
 * @property Societe $Societe
 */
class SocietesController extends AppController {
 
    public $paginate = array(
        'limit' => 15,
        'order' => array('Societe.NOM' => 'asc'),
        /*'order' => array(
            'Post.title' => 'asc' /*/
        );
    
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Societe->recursive = 0;
		$this->set('societes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Societe->exists($id)) {
			throw new NotFoundException(__('Société incorrecte'),true,array('class'=>'alert alert-error'));
		}
		$options = array('conditions' => array('Societe.' . $this->Societe->primaryKey => $id));
		$this->set('societe', $this->Societe->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Societe->create();
			if ($this->Societe->save($this->request->data)) {
				$this->Session->setFlash(__('Société sauvegardée'),true,array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Société incorrecte, veuillez corriger la société'),true,array('class'=>'alert alert-error'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Societe->exists($id)) {
			throw new NotFoundException(__('Société incorrecte'),true,array('class'=>'alert alert-error'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Societe->save($this->request->data)) {
				$this->Session->setFlash(__('Société sauvegardée'),true,array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Société incorrecte, veuillez corriger la société'),true,array('class'=>'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('Societe.' . $this->Societe->primaryKey => $id));
			$this->request->data = $this->Societe->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Societe->id = $id;
		if (!$this->Societe->exists()) {
			throw new NotFoundException(__('Société incorrecte'),true,array('class'=>'alert alert-error'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Societe->delete()) {
			$this->Session->setFlash(__('Société supprimé'),true,array('class'=>'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Société <b>NON</b> supprime'),true,array('class'=>'alert alert-error'));
		$this->redirect(array('action' => 'index'));
	}
        
/**
 * search method
 *
 * @return void
 */
	public function search() {
                $keyword=$this->params->data['Societe']['SEARCH']; 
                $newconditions = array('OR'=>array("Societe.NOM LIKE '%".$keyword."%'","Societe.NOMCONTACT LIKE '%".$keyword."%'","Societe.TELEPHONE LIKE '%".$keyword."%'"));
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions));
                $this->autoRender = false;
                $this->Societe->recursive = 0;
                $this->set('societes', $this->paginate());              
                $this->render('/Societes/index');
        }            
}
