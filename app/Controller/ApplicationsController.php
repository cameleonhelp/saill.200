<?php
App::uses('AppController', 'Controller');
App::uses('AssoentiteutilisateursController', 'Controller');
App::uses('EntitesController', 'Controller');
/**
 * Applications Controller
 *
 * @property Application $Application
 * @property PaginatorComponent $Paginator
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class ApplicationsController extends AppController {

    /**
     * Déclaration des variables
     */
    public $paginate = array('limit' => 25,'order'=>array('Application.NOM'=>'asc'));
    public $components = array('History','Common');

    /**
     * cherche le périmètre de visibilité
     * 
     * @return string
     */
    public function get_visibility(){
        if(userAuth('profil_id')==1):
            return null;
        else:
            $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
            return $ObjAssoentiteutilisateurs->json_get_my_entite(userAuth('id'));
        endif;
    }

    /**
     * limite la visibilité des applications
     * 
     * @param string $visibility
     * @return string
     */
    public function get_restriction($visibility){
        if($visibility == null):
            return '1=1';
        elseif ($visibility!=''):
            return array('Application.entite_id IN ('.$visibility.')');
        else:
            return array('Application.entite_id ='.userAuth('entite_id'));
        endif;
    }

    /**
     * Filtre les application par leur état actif ou pas
     * 
     * @param int $id état
     * @return string
     */
    public function get_application_actif_filter($id){
        $result = array();
        switch($id):
            case null:
            case 1:
                $result['condition']="Application.ACTIF=1";
                $result['filter'] = 'actives';
                break;
            case 0:
                $result['condition']="Application.ACTIF=0";
                $result['filter'] = 'inactives';
                break;
        endswitch;
        return $result;
    }

    /**
     * Retourne l'entité d'une application
     * 
     * @param int $app_id
     * @return int
     */
    public function get_entite_id($app_id){
        $obj = $this->Application->findById($app_id);
        return $obj['Application']['entite_id']!=null ? $obj['Application']['entite_id'] : userAuth('entite_id');
    }

    /**
     * filtre les application par entité
     * 
     * @param string $id filtre
     * @param string $visibility
     * @return string
     */
    public function get_application_entite_filter($id,$visibility){
        $result = array();
        switch($id):
            case null:
            case 'tous':
                if($visibility == null):
                    $result['condition']='1=1';
                elseif ($visibility!=''):
                    $result['condition']=array('OR'=>array('Application.entite_id IN ('.$visibility.')','Application.entite_id IS NULL'));
                else:
                    $result['condition']=array('OR'=>array('Application.entite_id ='.userAuth('entite_id'),'Application.entite_id IS NULL'));
                endif;                      
                $result['filter'] = ' de tous les cercles';
                break;
            default:
                $result['condition']='Application.entite_id ='.$id;
                $ObjEntites = new EntitesController();
                $nom = $ObjEntites->get_entite_nom($id);
                $result['filter'] = 'ayant pour entité '.$nom;
        endswitch;
        return $result;
    }          

    /**
     * Retourne la liste des applications
     * 
     * @param int $actif
     * @param int $entite
     * @throws UnauthorizedException
     * @return Applications
     */
    public function index($actif=null,$entite=null) {
        if (isAuthorized('applications', 'index')) :
            $visibility = $this->get_visibility();                
            $restriction= $this->get_restriction($visibility);
            $getactif = $this->get_application_actif_filter($actif);
            $getentite = $this->get_application_entite_filter($entite, $visibility);
            $this->set('strfilter',$getactif['filter'].$getentite['filter']);
            $newcondition = array($restriction,$getactif['condition'],$getentite['condition']);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newcondition,'recursive'=>0));                 
            $this->set('applications', $this->paginate());
            $ObjEntites = new EntitesController();
            $cercles = $ObjEntites->get_all();
            $this->set(compact('cercles'));                
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * Ajout d'une nouvelle application
     * 
     * @throws UnauthorizedException
     * @return void
     */
    public function add() {
        if (isAuthorized('applications', 'add')) :
            if ($this->request->is('post')) :
                if (isset($this->params['data']['cancel'])) :
                    $this->Application->validate = array();
                    $this->History->goBack(1);
                else:                    
                    $this->request->data['Application']['entite_id']=userAuth('entite_id');
                    $this->Application->create();
                    if ($this->Application->save($this->request->data)) {
                            $this->Session->setFlash(__('Application sauvegardée',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Application incorrecte, veuillez corriger l\'application',true),'flash_failure');
                    }
                endif;
            endif;
            $ObjEntites = new EntitesController();
            $cercles = $ObjEntites->find_list_cercle();
            $this->set(compact('cercles'));                 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * Modification de l'application
     * 
     * @param int $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @return void
     */
    public function edit($id = null) {
        if (isAuthorized('applications', 'edit')) :
            if (!$this->Application->exists($id)) {
                    throw new NotFoundException(__('Application incorrecte'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Application->validate = array();
                    $this->History->goBack(1);
                else:                    
                    if ($this->Application->save($this->request->data)) {
                            $this->Session->setFlash(__('Application sauvegardée',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Application incorrecte, veuillez corriger l\'application',true),'flash_failure');
                    }
                endif;
            } else {
                $options = array('conditions' => array('Application.' . $this->Application->primaryKey => $id));
                $this->request->data = $this->Application->find('first', $options);
                $ObjEntites = new EntitesController();
                $cercles = $ObjEntites->find_list_cercle();
                $this->set(compact('cercles'));                        
            }
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * Suppression de l'application
     * 
     * @param int $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @erturn void
     */
    public function delete($id = null) {
        if (isAuthorized('applications', 'delete')) :
            $this->Application->id = $id;
            if (!$this->Application->exists()) {
                    throw new NotFoundException(__('Application incorrecte'));
            }
            if ($this->Application->saveField('ACTIF',0)) {
                    $this->Session->setFlash(__('Application supprimée',true),'flash_success');
            } else {
                    $this->Session->setFlash(__('Application <b>NON</b> supprimée',true),'flash_failure');
            }
            $this->History->notmove();
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * Méthode dynamique pour rendre actif une application (Ajax)
     */
    public function ajax_actif(){
            $id = $this->request->data('id');
            $this->Application->id = $id;
            $application = $this->Application->find('first',array('conditions'=>array('Application.id'=>$id),'recursive'=>0));
            $newactif = $application['Application']['ACTIF'] == 1 ? 0 : 1;
            if ($this->Application->saveField('ACTIF',$newactif)) {
                    $this->Session->setFlash(__('Modification du statut actif pris en compte',true),'flash_success');
            } else {
                    $this->Session->setFlash(__('Modification du statut actif <b>NON</b> pris en compte',true),'flash_failure');
            }
            exit();
    }

    /**
     * Recherche les applications contenu dans $keywords
     * 
     * @param int $actif
     * @param int $entite
     * @param string $keywords
     * @throws UnauthorizedException
     * @return Applications
     */
    public function search($actif=null,$entite=null,$keywords=null){
        if (isAuthorized('applications', 'index')) :
            if(isset($this->params->data['Application']['SEARCH'])):
                $keywords = $this->params->data['Application']['SEARCH'];
            elseif (isset($keywords)):
                $keywords=$keywords;
            else:
                $keywords=''; 
            endif;
            $this->set('keywords',$keywords);
            if($keywords!= ''):
                $arkeywords = explode(' ',trim($keywords)); 
                $visibility = $this->get_visibility();                
                $restriction= $this->get_restriction($visibility);
                $getactif = $this->get_application_actif_filter($actif);
                $getentite = $this->get_application_entite_filter($entite, $visibility);
                $this->set('strfilter',$getactif['filter'].$getentite['filter']);
                $newcondition = array($restriction,$getactif['condition'],$getentite['condition']);
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Application.NOM LIKE '%".$value."%'"));
                endforeach;
                $conditions = array($newcondition,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions,'recursive'=>0));                 
                $this->set('applications', $this->paginate());
                $ObjEntites = new EntitesController();
                $cercles = $ObjEntites->get_all();
                $this->set(compact('cercles'));                    
            else:
                $this->redirect(array('action'=>'index',$actif,$entite));
            endif;
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;  
    }

    /**
     * retourne la liste de toutes les applications pour les select
     * 
     * @param boolean $actif
     * @param boolean $all
     * @return array
     */
    public function get_select($actif=1,$all=0){
        $visibility = $this->get_visibility();                
        $conditions= $this->get_restriction($visibility);         
        $list = $this->Application->find('list',array('fields'=>array('Application.id','Application.NOM'),'conditions'=>array('Application.ACTIF'=>$actif,$conditions),'order'=>array('Application.NOM'=>'asc'),'recursive'=>0));
        if ($all==1) $list = array_merge(array(0=>"Toutes les applications"),$list);
        return $list;
    }    

    /**
     * retourne toutes les applications
     * 
     * @param boolean $actif
     * @return array
     */
    public function get_list($actif=null){
        $visibility = $this->get_visibility();                
        $conditions[]= $this->get_restriction($visibility);               
        $conditions[] = $actif == null ? '1=1' : 'Application.ACTIF='.$actif;
        $list = $this->Application->find('all',array('fields'=>array('Application.id','Application.NOM'),'order'=>array('Application.NOM'=>'asc'),'conditions'=>$conditions,'recursive'=>-1));
        return $list;
    }      

    /**
     * retourne la liste des applications au format string
     * 
     * @param boolean $actif
     * @return string
     */
    public function get_str_list($actif=null){
        $list = '';
        $visibility = $this->get_visibility();                
        $conditions[]= $this->get_restriction($visibility);               
        $conditions[] = $actif == null ? '1=1' : 'Application.ACTIF='.$actif;
        $objs = $this->Application->find('all',array('fields'=>array('Application.id','Application.NOM'),'order'=>array('Application.id'=>'asc'),'conditions'=>$conditions,'recursive'=>0));
        foreach ($objs as $obj):
            $list .= $obj['Application']['id'].",";
        endforeach;
        return strlen($list) > 1 ? substr_replace($list ,"",-1) : '0';
    }

    /**
     * retourne l'objet en fonction de son nom
     * 
     * @param string $nom
     * @return Application
     */
    public function getbynom($nom){
        $this->Application->recursive = 0;
        $obj = $this->Application->findByNom($nom);
        return $obj;
    } 

    /**
     * Retourne le nom de l'appication en focntion de son identifiant
     * 
     * @param int $id
     * @return string
     */
    public function getname($id){
        $conditions[] = 'Application.id='.$id;
        $list = $this->Application->find('first',array('fields'=>array('Application.NOM'),'conditions'=>$conditions,'recursive'=>0));
        return $list['Application']['NOM'];
    }            
}
