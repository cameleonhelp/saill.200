<?php
App::uses('AppController', 'Controller');
App::uses('AssoentiteutilisateursController', 'Controller');
App::uses('EntitesController', 'Controller');
App::uses('LocalitesController', 'Controller');
/**
 * Chassis Controller
 *
 * @property Chassis $Chassis
 * @property PaginatorComponent $Paginator
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class ChassisController extends AppController {
    /**
     * Components
     */
    public $paginate = array('limit' => 25,'order'=>array('Chassis.NOM'=>'asc'));
    public $components = array('History','Common');

    /**
     * Méthode permettant de fixer le titre de la page
     * 
     * @param string $title
     * @return string
     */
    public function set_title($title = null){
        $title = $title==null ? "Châssis" : $title;
        return $this->set('title_for_layout',$title); //$this->fetch($title);
    }           

    /**
     * retourne la couverture de la visibilité de l'utilisateur
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
     * applique une restriction en fonction de la visibilité de l'utilisateur
     * 
     * @param string $visibility
     * @return string
     */
    public function get_restriction($visibility){
        if($visibility == null):
            return '1=1';
        elseif ($visibility!=''):
            return array('OR'=>array('Chassis.entite_id IN ('.$visibility.')','Chassis.entite_id IS NULL'));
        else:
            return array('OR'=>array('Chassis.entite_id ='.userAuth('entite_id'),'Chassis.entite_id IS NULL'));
        endif;
    }

    /**
     * filtre les chassis s'ils sont actif ou non
     * 
     * @param string $id
     * @return string
     */
    public function get_chassis_actif_filter($id){
        $result = array();
        switch($id):
            case null:
            case 1:
                $result['condition']="Chassis.ACTIF=1";
                $result['filter'] = 'actives';
                break;
            case 0:
                $result['condition']="Chassis.ACTIF=0";
                $result['filter'] = 'inactives';
                break;
        endswitch;
        return $result;
    }

    /**
     * filtre les chasiis en fonction de leur localité
     * 
     * @param string $id
     * @return string
     */
    public function get_chassis_localite_filter($id){
        $result = array();
        switch($id):
                case null:
                case 'tous':
                    $result['condition']="1=1";
                    $result['filter']= ' de toutes les localités';
                    break;
                default:
                    $result['condition']="Chassis.localite_id=".$id;
                    $nomchassis = $this->Chassis->Localite->findById($id);
                    $result['filter']= ' se situant à '.$nomchassis['Localite']['NOM'];
                    break;
        endswitch;
        return $result;
    }

    /**
     * filtre les chassis en fonction de leur entité
     * 
     * @param string $id
     * @param string $visibility
     * @return string
     */
    public function get_chassis_entite_filter($id,$visibility){
        $result = array();
        switch($id):
            case null:
            case 'tous':
                if($visibility == null):
                    $result['condition']='1=1';
                elseif ($visibility!=''):
                    $result['condition']=array('OR'=>array('Chassis.entite_id IN ('.$visibility.')','Chassis.entite_id IS NULL'));
                else:
                    $result['condition']=array('OR'=>array('Chassis.entite_id ='.userAuth('entite_id'),'Chassis.entite_id IS NULL'));
                endif;                      
                $result['filter'] = ' de tous les cercles';
                break;
            default:
                $result['condition']='Chassis.entite_id ='.$id;
                $ObjEntites = new EntitesController();	
                $nom = $ObjEntites->get_entite_nom($id);
                $result['filter'] = 'ayant pour entité '.$nom;
        endswitch;
        return $result;
    }      
    
    /**
     * liste les chasiis
     *
     * @param string $actif
     * @param string $localite
     * @param string $entite
     * @throws UnauthorizedException
     * @return void
     */
    public function index($actif=null,$localite=null,$entite=null) {
        $this->set_title();
        if (isAuthorized('chassis', 'index')) :
            $visibility = $this->get_visibility();                
            $restriction= $this->get_restriction($visibility);
            $getactif = $this->get_chassis_actif_filter($actif);
            $getlocalite = $this->get_chassis_localite_filter($localite);
            $getentite = $this->get_chassis_entite_filter($entite, $visibility);
            $this->set('strfilter',$getactif['filter'].$getlocalite['filter'].$getentite['filter']);
            $newcondition = array($restriction,$getactif['condition'],$getlocalite['condition'],$getentite['condition']);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newcondition,'recursive'=>0));   
            $this->set('chassis', $this->paginate());
            $ObjEntites = new EntitesController();
            $ObjLocalites = new LocalitesController();
            $cercles = $ObjEntites->get_all();
            $localites = $ObjLocalites->get_list();
            $this->set(compact('localites','cercles'));
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * ajoute un chassis
     *
     * @return void
     */
    public function add() {
        $this->set_title();
        if (isAuthorized('chassis', 'add')) :
            if ($this->request->is('post')) :
                if (isset($this->params['data']['cancel'])) :
                    $this->Chassis->validate = array();
                    $this->History->goBack(1);
                else:                
                    $this->request->data['Chassis']['entite_id']=userAuth('entite_id');
                    $this->Chassis->create();
                    if ($this->Chassis->save($this->request->data)) {
                            $this->Session->setFlash(__('Chassis sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Chassis incorrect, veuillez corriger le chassis',true),'flash_failure');
                    }
                endif;
            endif;
            $ObjEntites = new EntitesController();
            $ObjLocalites = new LocalitesController();
            $cercles = $ObjEntites->find_list_cercle();
            $localites = $ObjLocalites->get_select(1);                
            $this->set(compact('cercles','localites'));   
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * met à jour le chassis
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->set_title();
        if (isAuthorized('chassis', 'edit')) :            
            if (!$this->Chassis->exists($id)) {
                    throw new NotFoundException(__('Chassis incorrect'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Chassis->validate = array();
                    $this->History->goBack(1);
                else:                    
                    if ($this->Chassis->save($this->request->data)) {
                            $this->Session->setFlash(__('Chassis sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Chassis incorrect, veuillez corriger l\'application',true),'flash_failure');
                    }
                endif;
            } else {
                $options = array('conditions' => array('Chassis.' . $this->Chassis->primaryKey => $id));
                $this->request->data = $this->Chassis->find('first', $options);
                $ObjEntites = new EntitesController();
                $ObjLocalites = new LocalitesController();
                $cercles = $ObjEntites->find_list_cercle();
                $localites = $ObjLocalites->get_select(1);                       
                $this->set(compact('cercles','localites'));                      
            }
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * supprime le chassis
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->set_title();
        if (isAuthorized('chassis', 'delete')) : 
            $this->Chassis->id = $id;
            if (!$this->Chassis->exists()) {
                    throw new NotFoundException(__('Chassis incorrect'));
            }
            if ($this->Chassis->saveField('ACTIF',0)) {
                    $this->Session->setFlash(__('Chassis supprimé',true),'flash_success');
            } else {
                    $this->Session->setFlash(__('Chassis <b>NON</b> supprimé',true),'flash_failure');
            }
            $this->History->notmove();
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                  
    }

    /**
     * change l'état actif du chassis (Ajax)
     */
    public function ajax_actif(){
            $id = $this->request->data('id');
            $this->Chassis->id = $id;
            $obj = $this->Chassis->find('first',array('conditions'=>array('Chassis.id'=>$id),'recursive'=>0));
            $newactif = $obj['Chassis']['ACTIF'] == 1 ? 0 : 1;
            if ($this->Chassis->saveField('ACTIF',$newactif)) {
                    $this->Session->setFlash(__('Modification du statut actif pris en compte',true),'flash_success');
            } else {
                    $this->Session->setFlash(__('Modification du statut actif <b>NON</b> pris en compte',true),'flash_failure');
            }
            exit();
    }

    /**
     * recherche des chassis
     * 
     * @param string $actif
     * @param string $localite
     * @param string $entite
     * @param string $keywords
     * @throws UnauthorizedException
     */
    public function search($actif=null,$localite=null,$entite=null,$keywords=null) {
        $this->set_title();
        if (isAuthorized('chassis', 'index')) :
            if(isset($this->params->data['Chassis']['SEARCH'])):
                $keywords = $this->params->data['Chassis']['SEARCH'];
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
                $getactif = $this->get_chassis_actif_filter($actif);
                $getlocalite = $this->get_chassis_localite_filter($localite);
                $getentite = $this->get_chassis_entite_filter($entite, $visibility);
                $this->set('strfilter',$getactif['filter'].$getlocalite['filter'].$getentite['filter']);
                $newcondition = array($restriction,$getactif['condition'],$getlocalite['condition'],$getentite['condition']);
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Chassis.NOM LIKE '%".$value."%'","Localite.NOM LIKE '%".$value."%'","Chassis.NIVEAU LIKE '%".$value."%'","Chassis.ARMOIRE LIKE '%".$value."%'","Chassis.PVU LIKE '%".$value."%'"));
                endforeach;
                $conditions = array($newcondition,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions,'recursive'=>0));                 
                $this->set('chassis', $this->paginate());
                $ObjEntites = new EntitesController();
                $ObjLocalites = new LocalitesController();
                $cercles = $ObjEntites->get_all();
                $localites = $ObjLocalites->get_list();                    
                $this->set(compact('localites','cercles'));
            else:
                $this->redirect(array('action'=>'index',$actif,$localite,$entite));
            endif; 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;  
    }

    /**
     * retourne la liste des chassis
     * 
     * @param string $actif
     * @return array
     */
    public function get_select($actif=null){
        $visibility = $this->get_visibility();                
        $conditions[]= $this->get_restriction($visibility);               
        $conditions[] = $actif == null ? '1=1' : 'Localite.ACTIF='.$actif;    
        $list = $this->Chassis->find('list',array('fields'=>array('Chassis.id','Chassis.NOM'),'conditions'=>$conditions,'order'=>array('Chassis.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }    

    /**
     * retourne le chassis à partir de son nom
     * 
     * @param string $nom
     * @return array
     */
    public function getbynom($nom){
        $this->Chassis->recursive = 0;
        $obj = $this->Chassis->findByNom($nom);
        return $obj;
    }    
    
    /**
     * retourne la liste des chassis
     * 
     * @param string $like     
     * @param string $actif
     * @return array
     */
    public function get_select_for($like=null,$actif=null){
        $visibility = $this->get_visibility();                
        $conditions[]= $this->get_restriction($visibility); 
        $conditions[] = $like == null ? '1=1' : 'Chassis.NOM LIKE "'.$like.'%"'; 
        $conditions[] = $actif == null ? '1=1' : 'Localite.ACTIF='.$actif;    
        $list = $this->Chassis->find('list',array('fields'=>array('Chassis.id','Chassis.NOM'),'conditions'=>$conditions,'order'=>array('Chassis.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }        
}
