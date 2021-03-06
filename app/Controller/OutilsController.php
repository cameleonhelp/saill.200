<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Assoentiteutilisateurs');
/**
 * Outils Controller
 *
 * @property Outil $Outil
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class OutilsController extends AppController {
    /**
     * variables gloabales utilisées au niveau du controller
     */
    public $components = array('History','Common');
    public $paginate = array(
    'limit' => 25,
    'order' => array('Outil.NOM' => 'asc'),
    );

    /**
     * calcule le périmètre de visibilité
     * 
     * @return string
     */
    public function get_visibility(){
        if(userAuth('profil_id')==1):
            return null;
        else:
            $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
            return $ObjAssoentiteutilisateurs->json_get_all_users_actif_nogenerique(userAuth('id'));
        endif;
    }

    /**
     * applique le péréimètre de visibilité
     * 
     * @param string $visibility
     * @return string
     */
    public function get_restriction($visibility){
        if($visibility == null):
            return '1=1';
        elseif ($visibility!=''):
            return array('OR'=>array('Outil.utilisateur_id IN ('.$visibility.')','Outil.utilisateur_id IS NULL'));
        else:
            return array('OR'=>array('Outil.utilisateur_id ='.userAuth('id'),'Outil.utilisateur_id IS NULL'));
        endif;
    }

    /**
     * renvois la liste des utilisateurs pour un select
     * 
     * @param string $visibility
     * @return array
     */
    public function get_list_utilisateur($visibility){
        if($visibility == null):
             $condition = array("Utilisateur.id > 1","Utilisateur.profil_id > 0","Utilisateur.ACTIF"=>1);
        elseif ($visibility!=''):
            $condition = array("Utilisateur.id > 1","Utilisateur.profil_id > 0","Utilisateur.ACTIF"=>1,'Utilisateur.id IN ('.$visibility.')');
        else:
            $condition = array("Utilisateur.id > 1","Utilisateur.profil_id > 0","Utilisateur.ACTIF"=>1,'Utilisateur.id ='.userAuth('id'));
        endif;            
        return $this->Outil->Utilisateur->find('list',array('fields' => array('Utilisateur.id', 'Utilisateur.NOMLONG'),'order'=>array('Utilisateur.NOMLONG'=>'asc'),'conditions'=>$condition));              
    }        

    /**
     * liste les outils
     * 
     * @throws UnauthorizedException
     */
    public function index() {
        if (isAuthorized('outils', 'index')) :
            $listusers = $this->get_visibility();
            $newcondition = $this->get_restriction($listusers);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newcondition,'recursive'=>0));                 
            $this->set('outils', $this->paginate());
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * ajoute un nouvel outil
     * 
     * @throws UnauthorizedException
     */
    public function add() {
        if (isAuthorized('outils', 'add')) :           
            if ($this->request->is('post')) :
                if (isset($this->params['data']['cancel'])) :
                    $this->Outil->validate = array();
                    $this->History->goBack(1);
                else:                     
                    $this->Outil->create();
                    if ($this->Outil->save($this->request->data)) :
                            $this->Session->setFlash(__('Outil sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    else :
                            $this->Session->setFlash(__('Outil incorrect, veuillez corriger l\'outil',true),'flash_failure');
                    endif;
                endif;
            endif;
            $listusers = $this->get_visibility();
            $gestionnaire = $this->get_list_utilisateur($listusers);
            $this->set('gestionnaire',$gestionnaire);                 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * met à jour l'outil
     * 
     * @param string $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function edit($id = null) {
        if (isAuthorized('outils', 'edit')) :         
            if (!$this->Outil->exists($id)) {
                    throw new NotFoundException(__('Outil incorrect'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Outil->validate = array();
                    $this->History->goBack(1);
                else:                    
                    if ($this->Outil->save($this->request->data)) {
                            $this->Session->setFlash(__('Outil sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Outil incorrect, veuillez corriger l\'outil',true),'flash_failure');
                    }
                endif;
            } else {
                $options = array('conditions' => array('Outil.' . $this->Outil->primaryKey => $id),'recursive'=>0);
                $this->request->data = $this->Outil->find('first', $options);
                $listusers = $this->get_visibility();
                $gestionnaire = $this->get_list_utilisateur($listusers);
                $this->set('gestionnaire',$gestionnaire);                        
            }
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * supprime l'outil
     * 
     * @param string $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function delete($id = null) {
        if (isAuthorized('outils', 'delete')) :
            $this->Outil->id = $id;
            if (!$this->Outil->exists()) {
                    throw new NotFoundException(__('Outil incorrect'));
            }
            if ($this->Outil->delete()) {
                    $this->Session->setFlash(__('Outil supprimé',true),'flash_success');
                    $this->History->goBack(1);
            }
            $this->Session->setFlash(__('Outil NON supprimé',true),'flash_failure');
            $this->History->goBack(1);
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * recherche d'outils
     * 
     * @param string $keywords
     * @throws UnauthorizedException
     */
    public function search($keywords=null) {
        if (isAuthorized('outils', 'index')) :
            if(isset($this->params->data['Outil']['SEARCH'])):
                $keywords = $this->params->data['Outil']['SEARCH'];
            elseif (isset($keywords)):
                $keywords=$keywords;
            else:
                $keywords=''; 
            endif;
            $this->set('keywords',$keywords);
            if($keywords!= ''):
                $arkeywords = explode(' ',trim($keywords)); 
                $listusers = $this->get_visibility();
                $newcondition = $this->get_restriction($listusers);
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Outil.NOM LIKE '%".$value."%'","Outil.DESCRIPTION LIKE '%".$value."%'","(CONCAT(Utilisateur.NOM, ' ', Utilisateur.PRENOM)) LIKE '%".$value."%'"));
                endforeach;
                $conditions = array($newcondition,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions,'recursive'=>0));                 
                $this->set('outils', $this->paginate());     
            else:
                $this->redirect(array('action'=>'index'));
            endif;
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }  

    /**
     * liste les outils pour un select
     * 
     * @param string $id de l'entité
     * @return array
     */
    public function get_list_outil($id = null){
        if ($id== null):
            $visibility = $this->get_visibility();
            $conditions = $this->get_restriction($visibility);
        else:
            $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
            $visibility = $ObjAssoentiteutilisateurs->json_get_all_users_actif_nogenerique_for_entite($id);
            $conditions = $this->get_restriction($visibility);
        endif;
        $list = $this->Outil->find('list',array('fields'=>array('id','NOM'),'conditions'=>$conditions,'order'=>array('Outil.NOM'=>'asc'),"recursive"=>1));
        return $list;
    }

    /**
     * liste des outils
     * 
     * @return array
     */
    public function get_all_outil(){
        $visibility = $this->get_visibility();
        $conditions = $this->get_restriction($visibility);
        $list = $this->Outil->find('all',array('conditions'=>$conditions,'order'=>array('Outil.NOM'=>'asc'),"recursive"=>1));
        return $list;
    }        
}
