<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Sections');
App::import('Controller', 'Utilisateurs');
App::import('Controller', 'Projets');
App::import('Controller', 'Assoentiteutilisateurs');
App::import('Controller', 'Outils');
App::import('Controller', 'Dossierpartages');
/**
 * Entites Controller
 *
 * @property Entite $Entite
 * @property PaginatorComponent $Paginator
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class EntitesController extends AppController {

    /**
     * Variables globales utilisées au niveau du controller
     */
    public $components = array('History','Common');  
    public $paginate = array(
    'limit' => 25,
    'order' => array('Entite.NOM' => 'asc'),
    );
        
    /**
     * Méthode permettant de fixer le titre de la page
     * 
     * @param string $title
     * @return string
     */
    public function set_title($title = null){
        $title = $title==null ? "Cercles de visibilité" : $title;
        return $this->set('title_for_layout',$title); //$this->fetch($title);
    }              

    /**
     * limite la visibilité de l'utilisateur
     * 
     * @return null
     */
    public function get_visibility(){
    if(userAuth('profil_id')==1):
        return null;
    else:
        return $this->find_str_id_cercle(userAuth('id')); 
    endif;
    }

    /**
     * liste des cercles en fonction de la visibilité de l'utilisateur
     * 
     * @param string $visibility
     */
    public function get_cercle($visibility){
        if($visibility == null):
            $result['condition']='1=1';
        elseif ($visibility!=''):
            $result['condition']="Entite.id IN (".$visibility.")";
        else:
            $result['condition']="Entite.id =".userAuth('entite_id');
        endif; 
    }

    /**
     * donne le nom du cercle de visibilité
     * 
     * @param string $id
     * @return string
     */
    public function get_entite_nom($id){
        $obj = $this->Entite->find('first',array('fields'=>array('Entite.NOM'),'conditions'=>array('Entite.id'=>$id),'recursive'=>-1));
        return $obj['Entite']['NOM'];
    }

    /**
     * retourne toutes les entités
     * 
     * @return array
     */
    public function get_all(){
        $visibility = $this->get_visibility();                
        $conditions[]= $this->get_cercle($visibility);        
        $list = $this->Entite->find('all',array('conditions'=>$conditions,'order'=>array('Entite.NOM'=>'asc'),'recursive'=>-1));
        return $list;
    }             

    /**
     * liste les cercles de visibilité
     * 
     * @throws UnauthorizedException
     */
    public function index() {
        $this->set_title();
        if (isAuthorized('entites', 'index')) :   
            $listentite = $this->get_visibility();
            $getcercle = $this->get_cercle($listentite);
            $newcondition = array($getcercle['condition']);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newcondition,'recursive'=>0));
            $this->set('entites', $this->paginate());
            $ObjSections = new SectionsController();
            $ObjProjets = new ProjetsController();	
            $ObjUtilisateurs = new UtilisateursController();
            $list_sections = $ObjSections->getList();
            $all_utilisateurs = $ObjUtilisateurs->get_list_all_actif();
            $utilisateurs_select = null;
            $count_utilisateurs = 0;
            $all_projets = $ObjProjets->get_list_actif();
            $projets_select = $ObjProjets->get_list_projet();  
            $count_projets = 0;
            $this->set(compact('all_utilisateurs','utilisateurs_select','all_projets','projets_select','count_utilisateurs','count_projets','list_sections'));
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif; 
    }

    /**
     * ajoute un cercle de visibilité
     * 
     * @throws UnauthorizedException
     */
    public function add() {
        $this->set_title();
        if (isAuthorized('entites', 'add')) :
            if ($this->request->is('post')) :
                if (isset($this->params['data']['cancel'])) :
                    $this->Entite->validate = array();
                    $this->History->goBack(1);
                else:           
                    $this->request->data['Entite']['TEMPLATEOUTILS'] = isset($this->request->data['Entite']['TEMPLATEOUTILS']) ? implode(',',$this->request->data['Entite']['TEMPLATEOUTILS']) : null;
                    $this->request->data['Entite']['TEMPLATEGROUPE'] = isset($this->request->data['Entite']['TEMPLATEGROUPE']) ? implode(',',$this->request->data['Entite']['TEMPLATEGROUPE']) : null;
                    $this->Entite->create();
                    if ($this->Entite->save($this->request->data)) {
                            $this->Session->setFlash(__('Cercle de visibilté sauvegardé',true),'flash_success');
                            $this->History->goBack(1); 
                    } else {
                            $this->Session->setFlash(__('Cercle de visibilté incorrect, veuillez corriger le cercle de visibilté',true),'flash_failure');
                    }
                endif;
            endif;
            $ObjOutils = new OutilsController();
            $listoutil = $ObjOutils->get_list_outil();
            $this->set('listoutil',$listoutil);
            $ObjDossierpartages = new DossierpartagesController();
            $listgroup = $ObjDossierpartages->get_list_shared();
            $this->set('listgroup',$listgroup);                  
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * modification d'un cercle de visibilité
     * 
     * @param string $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function edit($id = null) {
         $this->set_title();
         if (isAuthorized('entites', 'edit')) :
            if (!$this->Entite->exists($id)) {
                    throw new NotFoundException(__('Cercle de visibilité invalide'));
            }
            if ($this->request->is(array('post', 'put'))) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Entite->validate = array();
                    $this->History->goBack(1);
                else:                      
                    $this->Entite->id = $id;
                    $this->request->data['Entite']['TEMPLATEOUTILS'] = isset($this->request->data['Entite']['TEMPLATEOUTILS']) ? implode(',',$this->request->data['Entite']['TEMPLATEOUTILS']) : null;
                    $this->request->data['Entite']['TEMPLATEGROUPE'] = isset($this->request->data['Entite']['TEMPLATEGROUPE']) ? implode(',',$this->request->data['Entite']['TEMPLATEGROUPE']) : null;
                    if ($this->Entite->save($this->request->data)) {
                            $this->Session->setFlash(__('Cercle de visibilté sauvegardé',true),'flash_success');
                            $this->History->goBack(1); 
                    } else {
                            $this->Session->setFlash(__('Cercle de visibilté incorrect, veuillez corriger le cercle de visibilté',true),'flash_failure');
                    }
                endif;
            } else {
                    $options = array('conditions' => array('Entite.' . $this->Entite->primaryKey => $id));
                    $this->request->data = $this->Entite->find('first', $options);
                    $ObjOutils = new OutilsController();
                    $listoutil = $ObjOutils->get_list_outil($id);
                    $this->set('listoutil',$listoutil);
                    $ObjDossierpartages = new DossierpartagesController();
                    $listgroup = $ObjDossierpartages->get_list_shared($id);
                    $this->set('listgroup',$listgroup);                          
            }
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * Suppression du cercle de visibilité
     * 
     * @param type $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function delete($id = null) {
        $this->set_title();
        if (isAuthorized('entites', 'delete')) :                
            $this->Entite->id = $id;
            if (!$this->Entite->exists()) {
                    throw new NotFoundException(__('Cercle de visibilité invalide'));
            }
            $this->request->onlyAllow('post', 'delete');
            if ($this->Entite->delete()) {
                    $this->Session->setFlash(__('Cercle de visibilté supprimé',true),'flash_success');
            } else {
                    $this->Session->setFlash(__('Cercle de visibilté <b>NON</b> supprimé',true),'flash_failure');
            }
            $this->History->goBack(1); 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * retourne tous les cercle de visibilité d'un utilisateur
     * 
     * @param string $utilisateur_id
     * @return array
     */
    public function find_all_cercle($utilisateur_id=null){
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $tmp = $utilisateur_id != null ? $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur_id) : $ObjAssoentiteutilisateurs->json_get_my_entite();
        $conditions = array();
        $conditions[]=array('Entite.id IN ('.$tmp.')');
        $order = array();
        $order[]=array('Entite.NOM'=>'asc');
        $list = $this->Entite->find('all',array('order'=>$order,'conditions'=>$conditions,'recursive'=>1));
        return $list;
    }

    /**
     * retourne tous les cercles de visibilité non vides pour un utilisateur  
     * 
     * @param string $utilisateur
     * @return array
     */
    public function find_all_cercle_not_empty($utilisateur=null){
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $tmp = $utilisateur != null ? $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur) : $ObjAssoentiteutilisateurs->json_get_my_entite();
        $sql = "select Entite.*, count(assoentiteutilisateurs.id) AS ASSO FROM entites AS Entite
                left join assoentiteutilisateurs on Entite.id = assoentiteutilisateurs.entite_id
                WHERE Entite.ACTIF = 1
                AND Entite.id IN (".$tmp.")
                group by assoentiteutilisateurs.entite_id";
        $cercles = $this->Entite->query($sql);
        $result = array();
        foreach ($cercles as $cercle):
            if ($cercle[0]['ASSO'] != 0):
                $result[]['Entite'] = $cercle['Entite'];
            endif;
        endforeach;
        return $result;
    }

    /**
     * renvois la liste au format chaine de caractère en fonction d'un utilisateur
     * 
     * @param string $utilisateur_id
     * @return string
     */
    public function find_str_id_cercle($utilisateur_id){
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $tmp = $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur_id);
        $conditions = array();  
        $conditions[]=array('Entite.id IN ('.$tmp.')');
        $order = array();
        $order[]=array('Entite.id'=>'asc');
        $objs = $this->Entite->find('all',array('order'=>$order,'conditions'=>$conditions,'recursive'=>-1));
        $list = '';
        if(count($objs) > 0):
            foreach($objs as $obj):
                $list .= $obj['Entite']['id'].",";
            endforeach;
            return substr_replace($list ,"",-1);
        else :
            return '0';
        endif;
    } 

    /**
     * retourne la liste des cercle pour un utilisateur
     * 
     * @param string $utilisateur_id
     * @return array
     */
    public function find_list_cercle($utilisateur_id=null){
        if(userAuth('profil_id')==1):
            $conditions[]=array('1=1');
            $order[]=array('Entite.NOM'=>'asc');
            $fields = array('Entite.id','Entite.NOM');
        else:            
            $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
            $tmp = $utilisateur_id != null ? $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur_id) : $ObjAssoentiteutilisateurs->json_get_my_entite();
            $conditions[]=array('Entite.id IN ('.$tmp.')');
            $order[]=array('Entite.NOM'=>'asc');
            $fields = array('Entite.id','Entite.NOM');
        endif;
        $list = $this->Entite->find('list',array('fields'=>$fields,'order'=>$order,'conditions'=>$conditions,'recursive'=>1));
        return $list;
    } 
    
    /**
     * retourne la liste de tous les cercles 
     * 
     * @return array
     */
    public function find_list_all_cercle(){
        $conditions[]=array('1=1');
        $order[]=array('Entite.NOM'=>'asc');
        $fields = array('Entite.id','Entite.NOM');
        $list = $this->Entite->find('list',array('fields'=>$fields,'order'=>$order,'conditions'=>$conditions,'recursive'=>1));
        return $list;
    }     

    /**
     * renvois la liste des cercles actifs
     * 
     * @return array
     */
    public function find_list_all_actif_cercle(){
        $conditions = array();
        $conditions[]=array('Entite.ACTIF'=>1);
        $order = array();
        $order[]=array('Entite.NOM'=>'asc');
        $fields = array('Entite.id','Entite.NOM');
        $list = $this->Entite->find('list',array('fields'=>$fields,'order'=>$order,'conditions'=>$conditions,'recursive'=>1));
        return $list;
    }   

    /**
     * recherche des cercles de visibilité
     * 
     * @param string $keywords
     * @throws UnauthorizedException
     */
    public function search($keywords=null) {
        $this->set_title();
        if (isAuthorized('entites', 'index')) :
            if(isset($this->params->data['Autorisation']['SEARCH'])):
                $keywords = $this->params->data['Autorisation']['SEARCH'];
            elseif (isset($keywords)):
                $keywords=$keywords;
            else:
                $keywords=''; 
            endif;
            $this->set('keywords',$keywords);
            if($keywords!= ''):
                $arkeywords = explode(' ',trim($keywords));                 
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Entite.NOM LIKE '%".$value."%'","Entite.COMMENTAIRE LIKE '%".$value."%'"));     
                endforeach;
                $listentite = $this->get_visibility();
                $getcercle = $this->get_cercle($listentite);
                $newcondition = array($getcercle['condition']);
                $ObjSections = new SectionsController();
                $ObjProjets = new ProjetsController();	
                $ObjUtilisateurs = new UtilisateursController();
                $list_sections = $ObjSections->getList();
                $all_utilisateurs = $ObjUtilisateurs->get_list_all_actif();
                $utilisateurs_select = null;
                $count_utilisateurs = 0;
                $all_projets = $ObjProjets->get_list_actif();
                $projets_select = $ObjProjets->get_list_projet();                      
                $count_projets = 0;
                $this->set(compact('all_utilisateurs','utilisateurs_select','all_projets','projets_select','count_utilisateurs','count_projets','list_sections'));                    
                $conditions = array($newcondition,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions));        
                $this->set('entites', $this->paginate());          
            else:
                $this->redirect(array('action'=>'index'));
            endif;
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    } 

    /**
     * renvois le contact pour l'envois des mails
     * 
     * @param string $entite
     * @return string
     */
    public function get_contact($entite=null){
        if($entite != null):
            $contact = $this->Entite->findById($entite);
            return $contact['Entite']['CONTACT'];
        else:
            return '';
        endif;
    }

    /**
     * renvois le memo de facturation pour le cercle de visibilité
     * 
     * @param string $entite
     * @return string
     */
    public function get_memo($entite=null){
        if($entite != null):
            $contact = $this->Entite->findById($entite);
            return $contact['Entite']['MEMOFACTURATION'];
        else:
            return '';
        endif;
    }    

    /**
     * enregistre le méméo de facturation pour le cercle de visibilité
     * 
     * @param string $entite
     * @param string $memo
     * @return boolean
     */
    public function set_memo($entite=null,$memo=null){
        $this->autoRender = false;
        $entite = $this->request->data('id');
        $memo = $this->request->data('memo');
        if($entite != null):
            $this->Entite->id = $entite;
            return $this->Entite->saveField('MEMOFACTURATION',$memo);
        else:
            return false;
        endif;
    }    

    /**
     * renvois la liste des groupes par défaut
     * 
     * @param string $entite
     * @return string
     */
    public function get_templategroup($entite=null){
        if($entite != null):
            $contact = $this->Entite->findById($entite);
            return $contact['Entite']['TEMPLATEGROUPE'];
        else:
            return '';
        endif;
    }    

    /**
     * enregistre la liste des groupes par défaut
     * 
     * @param type $entite
     * @param type $templategroupe
     * @return boolean
     */
    public function set_templategroup($entite=null,$templategroupe){
        if($entite != null):
            $this->Entite->id = $entite;
            return $this->Entite->saveField('TEMPLATEGROUPE',$templategroupe);
        else:
            return false;
        endif;
    }

    /**
     * renvois la liste des outils par défaut
     * 
     * @param string $entite
     * @return string
     */
    public function get_templateoutils($entite=null){
        if($entite != null):
            $contact = $this->Entite->findById($entite);
            return $contact['Entite']['TEMPLATEOUTILS'];
        else:
            return '';
        endif;
    }    

    /**
     * enregistre la liste des outils par défaut
     * 
     * @param type $entite
     * @param type $templateoutils
     * @return boolean
     */
    public function set_templateoutils($entite=null,$templateoutils){
        if($entite != null):
            $this->Entite->id = $entite;
            return $this->Entite->saveField('TEMPLATEOUTILS',$templateoutils);
        else:
            return false;
        endif;
    }            
}
