<?php
App::uses('AppController', 'Controller');
App::uses('AssoentiteutilisateursController', 'Controller');
App::uses('ProjetsController', 'Controller');
App::uses('ContratsController', 'Controller');
/**
 * Assoprojetentites Controller
 *
 * @property Assoprojetentite $Assoprojetentite
 * @property PaginatorComponent $Paginator
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class AssoprojetentitesController extends AppController {

    /**
     * variables globales utilisées au niveau du controller
     */
    public $components = array('History','Common');  
        
    /**
     * Sauvegarde l'association
     */
    public function save() {
        $this->autoRender = false;
        $bsave = array();
        $listusers = $this->request->data['Assoprojetentite']['projets_id'];
        $entite = $this->request->data['Assoprojetentite']['entite_id'];
        if($entite != ''):
            $users = explode(',',$listusers);
            $sql = "DELETE FROM assoprojetentites WHERE entite_id=".$entite;
            $this->Assoprojetentite->query($sql);
            if ($listusers != ''):
                foreach($users as $key=>$value):
                    $record = array();
                    $fields = array('Assoprojetentite.id');
                    $conditions[] = array('Assoprojetentite.entite_id'=>$entite);
                    $record['Assoprojetentite']['entite_id']= $entite;
                    $conditions[] = array('Assoprojetentite.projet_id'=>$value);
                    $record['Assoprojetentite']['projet_id']= $value;
                    $this->Assoprojetentite->create();
                    $record['Assoprojetentite']['created']= date('Y-m-d H:i:s');
                    $record['Assoprojetentite']['modified']= date('Y-m-d H:i:s');
                    if($this->Assoprojetentite->save($record)):
                        $bsave[] = true;
                    else:
                        $bsave[] = false;
                    endif;
                endforeach;
                if(in_array(false, $bsave)):
                    $this->Session->setFlash(__('Certaines informations sont incorrectes et ne sont pas sauvegardées',true),'flash_warning');
                else:
                    $this->Session->setFlash(__('Informations sauvegardées',true),'flash_success');
                endif;
            else:
                $this->Session->setFlash(__('Informations sauvegardées',true),'flash_success');
            endif;
        else:
            $this->Session->setFlash(__('Informations sur le cercle de visibilité incorrectes, veuillez corriger les informations',true),'flash_failure');
        endif;
        $this->History->goBack(1);
    }    

    /**
     * trouve les projets pour une entité
     * 
     * @param int $entite
     * @return string
     */
    public function json_get_projets($entite=null){
        $this->autoRender = false;
        $list = '';
        $obj = $this->Assoprojetentite->find('all', array('fields'=>array('Assoprojetentite.projet_id'),'conditions' => array('Assoprojetentite.entite_id' => $entite),'recursive'=>0));
        $results = isset($obj) ? $obj : 'null';
        if($results!='null'):
            foreach ($results as $result):
                $list .= $result['Assoprojetentite']['projet_id'].',';
            endforeach;
            return substr_replace($list ,"",-1);
        else:
            return '0';
        endif;                 
    }

    /**
     * compte les associations pour une entité
     * 
     * @param int $entite
     * @return int
     */
    public function count($entite=null){
        if($entite != null):
            $count = $this->Assoprojetentite->find('count',array('conditions' => array('Assoprojetentite.entite_id' => $entite),'recursive'=>0));
            return $count;
        endif;
    }

    /**
     * trouve tous les projet pour une entité
     * 
     * @param int $entite
     * @return string
     */
    public function json_get_all_projets_entite($entite){
        $this->autoRender = false;
        $list = '';
        $obj = $this->Assoprojetentite->find('all', array('conditions' => array('Assoprojetentite.entite_id IN ('.$entite.')'),'group'=>'Assoprojetentite.projet_id','order'=>array("Projet.id"=>"asc"),'recursive'=>0));
        $results = isset($obj) ? $obj : 'null';
        if($results!='null'):
            foreach ($results as $result):
                $list .= $result['Assoprojetentite']['projet_id'].',';
            endforeach;
            return substr_replace($list ,"",-1);
        else:
            return '0';
        endif;                   
    }  

    /**
     * trouve tous les projets pour un utilisateur
     * 
     * @param int $id utilisateur
     * @return string
     */
    public function json_get_all_projets($id){
        $this->autoRender = false;
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $entite = $ObjAssoentiteutilisateurs->json_get_my_entite($id);
        if($entite != '0'):
            $list = '';
            $obj = $this->Assoprojetentite->find('all', array('conditions' => array('Assoprojetentite.entite_id IN ('.$entite.')'),'group'=>'Assoprojetentite.projet_id','order'=>array('Assoprojetentite.projet_id'=>"asc"),'recursive'=>0));
            $results = isset($obj) ? $obj : 'null';               
            if($results!='null'):
                foreach ($results as $result):
                    $list .= $result['Assoprojetentite']['projet_id'].',';
                endforeach;
                return substr_replace($list ,"",-1);
            else:
                return '0';
            endif;    
        else:
            return '0';
        endif;              
    }  

    /**
     * liste des identifiants des contrats pour un utilisateur
     * 
     * @param int $utilisateur_id
     * @return string
     */
    public function find_str_id_contrats($utilisateur_id){
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $entite = $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur_id);            
        if($entite != '0'):
            $list = '';
            $obj = $this->Assoprojetentite->find('all', array('conditions' => array('Assoprojetentite.entite_id IN ('.$entite.')','Projet.contrat_id > 1'),'group'=>'Projet.contrat_id','order'=>array('Assoprojetentite.projet_id'=>"asc"),'recursive'=>0));
            $results = isset($obj) ? $obj : 'null';               
            if($results!='null'):
                foreach ($results as $result):
                    $list .= $result['Projet']['contrat_id'].',';
                endforeach;
                $list = strlen($list) > 1 ? substr_replace($list ,"",-1) : '0';
            else:
                $list = '0';
            endif;    
        else:
            $list = '0';
        endif;
        $ObjContrats = new ContratsController();
        $list = $list.','.$ObjContrats->get_str_my_entite(userAuth('entite_id'));
        return $list;
    }

    /**
     * trouve tous les contrat de l'entité d'un utilisateur
     * 
     * @param int $utilisateur_id
     * @return array
     */
    public function find_all_contrats($utilisateur_id){
        $ObjAssoentiteutilisateurs = new AssoentiteutilisateursController();
        $entite = $ObjAssoentiteutilisateurs->json_get_my_entite($utilisateur_id);
        if($entite != '0'):
            $list = '';
            $obj = $this->Assoprojetentite->find('all', array('conditions' => array('Assoprojetentite.entite_id IN ('.$entite.')'),'group'=>'Projet.contrat_id','order'=>array('Assoprojetentite.projet_id'=>"asc"),'recursive'=>0));
            $results = isset($obj) ? $obj : array();               
            return $results;   
        else:
            return array();
        endif;
    }  

    /**
     * sauvegarde silencieuse de l'association
     * 
     * @param int $entite_id
     * @param int $projet_id
     */
    public function silent_save($entite_id,$projet_id){
        //supression de l'existant
        $sql = "DELETE FROM assoprojetentites WHERE projet_id=".$projet_id;
        $this->Assoprojetentite->query($sql);
        //sauvegarde du nouvel enregistrement
        $record = array();
        $record['Assoprojetentite']['entite_id']= $entite_id;
        $record['Assoprojetentite']['projet_id']= $projet_id;
        $record['Assoprojetentite']['created']= date('Y-m-d H:i:s');
        $record['Assoprojetentite']['modified']= date('Y-m-d H:i:s');
        $this->Assoprojetentite->create();
        $this->Assoprojetentite->save($record);           
    }     

    /**
     * mise à jour silencieuse de l'association
     * 
     * @param int $entite_id
     * @param int $projet_id
     */
    public function silent_update($entite_id,$projet_id){
        $this->Assoprojetentite->updateAll(array('entite_id'=>$entite_id), array('projet_id'=>$projet_id));        
    }          

    /**
     * trouve la première entité pour un contrat
     * 
     * @param int $contrat_id
     * @return int id de l'entité
     */
    public function find_first_entite_for_contrat($contrat_id){
        $ObjProjets = new ProjetsController();
        $listprojet = $ObjProjets->find_str_projet_for_contrat($contrat_id);
        $entite = $this->Assoprojetentite->find('first',array('conditions'=>array('Assoprojetentite.projet_id IN ('.$listprojet.')'),'recursive'=>-1));
        return isset($entite['Assoprojetentite']['entite_id']) ? $entite['Assoprojetentite']['entite_id'] : null;
    }
}
