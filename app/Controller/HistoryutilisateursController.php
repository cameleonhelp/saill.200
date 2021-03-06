<?php
App::uses('AppController', 'Controller');
/**
 * Historyutilisateurs Controller
 *
 * @property Historyutilisateur $Historyutilisateur
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class HistoryutilisateursController extends AppController {
        public $components = array('History','Common');
/**
 * index method
 *
 * @return void
 */
//	public function index() {
//		$this->Historyutilisateur->recursive = 0;
//		$this->set('historyutilisateurs', $this->paginate());
//	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
//	public function view($id = null) {
//		if (!$this->Historyutilisateur->exists($id)) {
//			throw new NotFoundException(__('Invalid historyutilisateur'));
//		}
//		$options = array('conditions' => array('Historyutilisateur.' . $this->Historyutilisateur->primaryKey => $id));
//		$this->set('historyutilisateur', $this->Historyutilisateur->find('first', $options));
//	}

/**
 * add method
 *
 * @return void
 */
//	public function add() {
//		if ($this->request->is('post')) {
//			$this->Historyutilisateur->create();
//			if ($this->Historyutilisateur->save($this->request->data)) {
//				$this->Session->setFlash(__('The historyutilisateur has been saved'));
//				$this->redirect(array('action' => 'index'));
//			} else {
//				$this->Session->setFlash(__('The historyutilisateur could not be saved. Please, try again.'));
//			}
//		}
//	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
//	public function edit($id = null) {
//		if (!$this->Historyutilisateur->exists($id)) {
//			throw new NotFoundException(__('Invalid historyutilisateur'));
//		}
//		if ($this->request->is('post') || $this->request->is('put')) {
//			if ($this->Historyutilisateur->save($this->request->data)) {
//				$this->Session->setFlash(__('The historyutilisateur has been saved'));
//				$this->redirect(array('action' => 'index'));
//			} else {
//				$this->Session->setFlash(__('The historyutilisateur could not be saved. Please, try again.'));
//			}
//		} else {
//			$options = array('conditions' => array('Historyutilisateur.' . $this->Historyutilisateur->primaryKey => $id));
//			$this->request->data = $this->Historyutilisateur->find('first', $options);
//		}
//	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
//	public function delete($id = null) {
//		$this->Historyutilisateur->id = $id;
//		if (!$this->Historyutilisateur->exists()) {
//			throw new NotFoundException(__('Invalid historyutilisateur'));
//		}
//		$this->request->onlyAllow('post', 'delete');
//		if ($this->Historyutilisateur->delete()) {
//			$this->Session->setFlash(__('Historyutilisateur deleted'));
//			$this->redirect(array('action' => 'index'));
//		}
//		$this->Session->setFlash(__('Historyutilisateur was not deleted'));
//		$this->redirect(array('action' => 'index'));
//	}
       
    /**
     * renvois la liste des historique utilisateur
     * 
     * @param string $id
     * @return array
     */    
    public function get_list($id){
        $options = array('Historyutilisateur1.utilisateur_id' => $id);
        return $this->Historyutilisateur->find('list',array('fields' => array('id', 'HISTORIQUE'),'conditions' => $options,'order'=>array('HISTORIQUE'=>'desc'),'recursive'=>0));
    }

    /**
     * renvois la liste des historique utilisateur
     * 
     * @param string $id
     * @return array
     */
    public function get_all($id){
        $options = array('Historyutilisateur.utilisateur_id' => $id);
        return $this->Historyutilisateur->find('all',array('conditions' => $options,'order'=>array('Historyutilisateur.id'=>'desc'),'recursive'=>0));
    }           
}
