<?php
App::uses('AppController', 'Controller');
App::uses('AssoprojetentitesController', 'Controller');
App::uses('TjmcontratsController', 'Controller');
/**
 * Contrats Controller
 *
 * @property Contrat $Contrat
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class ContratsController extends AppController {
    /**
     * Déclaration des variables globales utilisées au niveau du controller
     */
    public $components = array('History','Common');
    public $paginate = array(
    'limit' => 25,
    'order' => array('Contrat.NOM' => 'asc'),
    'conditions' => array('OR'=>array('Contrat.id >' => 1,'Contrat.id' => 0)),
    );

    /**
     * liste des contrat de mom entités principales
     * 
     * @param int $entite
     * @return string
     */
    public function get_str_my_entite($entite=null){
        $this->autoRender = false;
        $list = '';
        if ($entite!= null):
            $obj = $this->Contrat->find('all', array('conditions' => array('OR'=>array('Contrat.entite_id'=>$entite,'Contrat.entite_id IS NULL')),'order'=>array('Contrat.id'=>'asc'),'recursive'=>-1));
        else:
            $obj = null;
        endif;
        $results = isset($obj) ? $obj : 'null';
        if($results!='null'):            
            foreach ($results as $result):
                $list .= $result['Contrat']['id'].',';
            endforeach;
            return substr_replace($list ,"",-1);
        else:
            return '';
        endif; 
    }

    /**
     * limite la visibilité en fonction de l'utilisateur
     * 
     * @return null
     */
    public function get_visibility(){
        if(userAuth('profil_id')==1):
            return null;
        else:
            $ObjAssoprojetentites = new AssoprojetentitesController();
            return '0,'.$ObjAssoprojetentites->find_str_id_contrats(userAuth('id'));
//                return $this->get_str_my_entite(userAuth('entite_id'));
        endif;
    }

    /**
     * applique la restriction de visibilité
     * 
     * @param string $visibility
     * @return string
     */
    public function get_restriction($visibility){
        if($visibility == null):
            return '1=1';
        elseif ($visibility!=''):
            return "Contrat.id IN (".$visibility.')';
        else:
            return 'Contrat.entite_id ='.userAuth('entite_id'); ;
        endif; 
    }

    /**
     * filtre les contrats en fonction de l'état actif
     * 
     * @param string $filtre
     * @param string $visibility
     * @return string
     */
    public function get_contrat_filtre_filter($filtre,$visibility){
        $result = array();
        switch ($filtre){
            case 'tous':   
                if($visibility == null):
                    $result['condition']='1=1';
                elseif ($visibility!=''):
                    $result['condition']="Contrat.id IN (".$visibility.')';
                else:
                    $result['condition']='Contrat.entite_id ='.userAuth('entite_id'); 
                endif;   
                $result['filter'] = "tous les contrats";
                break;
            case 'actif':
            case null:   
                if($visibility == null):
                    $result['condition']='Contrat.ACTIF=1';
                elseif ($visibility!=''):
                    $result['condition']="Contrat.id IN (".$visibility.') AND Contrat.ACTIF=1';
                else:
                    $result['condition']="Contrat.id < 1 AND Contrat.ACTIF=1 AND Contrat.entite_id =".userAuth('entite_id');
                endif;                      
                $result['filter'] = "tous les contrats actifs";
                break;  
            case 'inactif':
                if($visibility == null):
                    $result['condition']='Contrat.ACTIF=0';
                elseif ($visibility!=''):
                    $result['condition']="Contrat.id IN (".$visibility.') AND Contrat.ACTIF=0';
                else:
                    $result['condition']="Contrat.id < 1 AND Contrat.ACTIF=0 AND ".'Contrat.entite_id ='.userAuth('entite_id');
                endif;  
                $result['filter'] = "tous les contrats inactifs";
                break;                     
        } 
       
        return $result;
    }  

    /**
     * liste les contrats
     * 
     * @param string $filtre
     * @throws UnauthorizedException
     */
    public function index($filtre=null) {
        if (isAuthorized('contrats', 'index')) :
            $listcontrats = $this->get_visibility();
            $getfiltre = $this->get_contrat_filtre_filter($filtre, $listcontrats);
            $newconditions = array($getfiltre['condition']);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions,'recursive'=>0)); 
            $this->set('contrats', $this->paginate());
            $this->set('fcontrat',$getfiltre['filter']);
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * ajoute un contrat
     *
     * @return void
     */
    public function add() {
        if (isAuthorized('contrats', 'add')) :         
            if ($this->request->is('post')) :
                if (isset($this->params['data']['cancel'])) :
                    $this->Contrat->validate = array();
                    $this->History->goBack(1);
                else:                    
                    $this->request->data['Contrat']['entite_id']= userAuth('entite_id');
                    $this->Contrat->create();
                    if ($this->Contrat->save($this->request->data)) {
                            $this->Session->setFlash(__('Contrat sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Contrat incorrect, veuillez corriger le contrat',true),'flash_failure');
                    }
                endif;
            endif;
            $ObjTjmcontrats = new TjmcontratsController();
            $tjmcontrats = $ObjTjmcontrats->get_list();
            $this->set(compact('tjmcontrats'));                    
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * met à jour le contrat
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (isAuthorized('contrats', 'edit')) :          
            if (!$this->Contrat->exists($id)) {
                    throw new NotFoundException(__('Contrat incorrect'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Contrat->validate = array();
                    $this->History->goBack(1);
                else:                    
                    if ($this->Contrat->save($this->request->data)) {
                            $this->Contrat->id = $id;
                            $contrat = $this->Contrat->read('ACTIF');   
                            if($contrat['Contrat']['ACTIF']==false):
                                $actif = 0;
                                App::uses('Controller', 'Projets');
                                $thisprojet = new ProjetsController();
                                $thisprojet->set_actif($id, $actif);
                            endif;                            
                            $this->Session->setFlash(__('Contrat sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Contrat incorrect, veuillez corriger le contrat',true),'flash_failure');
                    }
                endif;
            } else {
                $options = array('conditions' => array('Contrat.' . $this->Contrat->primaryKey => $id));
                $this->request->data = $this->Contrat->find('first', $options);
                $ObjTjmcontrats = new TjmcontratsController();
                $tjmcontrats = $ObjTjmcontrats->get_list();
                $this->set(compact('tjmcontrats'));                            
            }
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * supprime le contrat
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        if (isAuthorized('contrats', 'delete')) :
            $this->Contrat->id = $id;
            if (!$this->Contrat->exists()) {
                    throw new NotFoundException(__('Contrat incorrect'));
            }
            if ($this->Contrat->delete()) {
                    $this->Session->setFlash(__('Contrat supprimé',true),'flash_success');
                    $this->History->goBack(1);
            }
            $this->Session->setFlash(__('Contrat <b>NON</b> supprimé',true),'flash_failure');
            $this->History->goBack(1);
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }

    /**
     * recherche un contrat
     * 
     * @param string $filtre
     * @param string $keywords
     * @throws UnauthorizedException
     */
    public function search($filtre=null,$keywords=null) {
        if (isAuthorized('contrats', 'index')) :
            if(isset($this->params->data['Contrat']['SEARCH'])):
                $keywords = $this->params->data['Contrat']['SEARCH'];
            elseif (isset($keywords)):
                $keywords=$keywords;
            else:
                $keywords=''; 
            endif;
            $this->set('keywords',$keywords);
            if($keywords!= ''):
                $arkeywords = explode(' ',trim($keywords)); 
                $listcontrats = ''; //$this->get_visibility();
                $getfiltre = $this->get_contrat_filtre_filter($filtre, $listcontrats);
                $this->set('fcontrat',$getfiltre['filter']);
                $newconditions = array($getfiltre['condition']);
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Contrat.NOM LIKE '%".$value."%'","Contrat.DESCRIPTION LIKE '%".$value."%'","Contrat.ANNEEDEBUT LIKE '%".$value."%'","Contrat.ANNEEFIN LIKE '%".$value."%'"));
                endforeach;
                $conditions = array($newconditions,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions,'recursive'=>0));                
                $this->set('contrats', $this->paginate());
            else:
                $this->redirect(array('action'=>'index',$filtre));
            endif; 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    }      

    /**
     * liste des contrat
     * 
     * @param string $list : list ou all
     * @return type
     */
    public function get_list($list='list'){
        //$visibility = $this->get_visibility();                
        $conditions[]= 'Contrat.entite_id ='.userAuth('entite_id'); // $this->get_restriction($visibility);               
        $list = $this->Contrat->find($list,array('fields'=>array('Contrat.id','Contrat.NOM'),'conditions'=>$conditions,'order'=>array('Contrat.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }

    /**
     * liste des contrats
     * 
     * @return type
     */
    public function get_all(){
        //$visibility = $this->get_visibility();                
        $conditions[]= 'Contrat.entite_id='.userAuth('entite_id'); //$this->get_restriction($visibility);               
        $list = $this->Contrat->find('all',array('conditions'=>$conditions,'order'=>array('Contrat.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }    

    /**
     * liste les contrats autres que absences
     * 
     * @return type
     */
    public function get_list_no_absence(){
        //$visibility = $this->get_visibility();                
        $conditions[]= 'Contrat.entite_id ='.userAuth('entite_id'); // $this->get_restriction($visibility); 
        $conditions[]= 'Contrat.id > 1';
        $list = $this->Contrat->find('list',array('fields'=>array('Contrat.id','Contrat.NOM'),'conditions'=>$conditions,'order'=>array('Contrat.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }

    /**
     * liste les contrats autres que absences
     * 
     * @return type
     */
    public function get_all_no_absence(){
        //$visibility = $this->get_visibility();                
        $conditions[]= 'Contrat.entite_id ='.userAuth('entite_id'); // $this->get_restriction($visibility);  
        $conditions[]= 'Contrat.id > 1';            
        $list = $this->Contrat->find('all',array('conditions'=>$conditions,'order'=>array('Contrat.NOM'=>'asc'),'recursive'=>0));
        return $list;
    }            

    /**
     * retourne le nom du contrat
     *  
     * @param type $id
     * @return type
     */
    public function get_nom($id){
        $conditions[]="Contrat.id = ".$id;
        $list = $this->Contrat->find('first',array('conditions'=>$conditions,'order'=>array('Contrat.NOM'=>'asc'),'recursive'=>-1));
        return $list['Contrat']['NOM'];
    }
}
