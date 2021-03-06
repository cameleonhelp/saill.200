<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Applications');
App::import('Controller', 'Assoentiteutilisateurs');
App::import('Controller', 'Types');
App::import('Controller', 'Parimeters');
App::import('Controller', 'Perimetres');
App::import('Controller', 'Etats');
App::import('Controller', 'Composants');
App::import('Controller', 'Lots');
App::import('Controller', 'Phases');
App::import('Controller', 'Volumetries');
App::import('Controller', 'Puissances');
App::import('Controller', 'Architectures');
App::import('Controller', 'Historyexpbs');
App::import('Controller', 'Dsitenvs');
/**
 * Expressionbesoins Controller
 *
 * @property Expressionbesoin $Expressionbesoin
 * @property PaginatorComponent $Paginator
 * @version 3.0.1.002 le 28/05/2014 par Jacques LEVAVASSEUR
 */
class ExpressionbesoinsController extends AppController {

    /**
     * Components
     */
    public $paginate = array('limit' => 25,'order'=>array('Expressionbesoin.modified'=>'desc'));
    public $components = array('History','Common');

    /**
     * recherche la limite de visibilité de l'utilisateur
     * 
     * @return null
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
     * applique les restrictions de visibilité
     * 
     * @param string $visibility
     * @return string
     */
    public function get_restriction($visibility){
        if($visibility == null):
            return "1=1";
        elseif ($visibility !=""):
            return "Expressionbesoin.entite_id IN (".$visibility.')';
        else:
            return "Expressionbesoin.entite_id=".userAuth('entite_id');
        endif;
    }

    /**
     * filtre les expressions de besoins par application
     * 
     * @param string $application
     * @return string
     */
    public function get_environnement_application_filter($application){
        $result = array();
        switch($application):
            case null:
            case 'tous':
                $ObjApplications = new ApplicationsController();
                $listapp = $ObjApplications->get_str_list();
                $result['condition']="Expressionbesoin.application_id IN (".$listapp.")";
                $result['filter'] = ', pour toutes les applications';
                break;
            default :
                $result['condition']="Expressionbesoin.application_id=".$application;
                $nom = $this->Expressionbesoin->Application->findById($application);
                $result['filter'] = ', pour l\'application '.$nom['Application']['NOM'];
                break;
        endswitch;
        return $result;
    }

    /**
     * filtre par etat les expression de besoins
     * 
     * @param string $etat
     * @return string
     */
    public function get_environnement_etat_filter($etat){
        $result = array();
        switch($etat):
            case null:
            case 'actif':
                $result['condition']="Expressionbesoin.etat_id < 4";
                $result['filter']= ', avec un état actif';
                break;                          
            case 'tous':
                $result['condition']="1=1";
                $result['filter']= '';
                break;                         
            default :
                $result['condition']="Expressionbesoin.etat_id=".$etat;
                $nom = $this->Expressionbesoin->Etat->findById($etat);
                $result['filter']= ', '.$nom['Etat']['NOM'];
                break;                   
        endswitch;
        return $result;
    }

    /**
     * filtre par environnement les expression de besoins
     * 
     * @param string $type
     * @return string
     */
    public function get_environnement_type_filter($type){
        $result = array();
        switch($type):
            case null:
            case 'tous':
                $result['condition']="1=1";
                $result['filter']= ', pour tous les environnements';
                break;
            default:
                $result['condition']="Expressionbesoin.type_id=".$type;
                $nom = $this->Expressionbesoin->Type->findById($type);
                $result['filter']= ', pour l\'environnement '.$nom['Type']['NOM'];
                break;
        endswitch;
        return $result;
    }

    /**
     * filtre l'expression des besoins par pemrimetre
     * 
     * @param string $perimetre
     * @return string
     */
    public function get_environnement_perimetre_filter($perimetre){
        $result = array();
        switch($perimetre):
            case null:
            case 'tous':
                $result['condition']="1=1";
                $result['filter']= '';
                break;
            default:
                $result['condition']="Expressionbesoin.perimetre_id=".$perimetre;
                $nom = $this->Expressionbesoin->Perimetre->findById($perimetre);
                $result['filter']= ', pour le périmétre '.$nom['Perimetre']['NOM'];
                break;
        endswitch;
        return $result;
    }

    /**
     * sauvegarde dans le cache le résultat qui sera utilisé pour l'export
     * 
     * @param array $conditions
     */
    public function get_export($conditions){
        $this->Session->delete('xls_export');
        $export = $this->Expressionbesoin->find('all',array('conditions'=>$conditions,'order' => array('Expressionbesoin.id' => 'desc'),'recursive'=>0));
        $this->Session->write('xls_export',$export); 
    }

    /**
     * fixe le titre de la page
     */
    public function set_title(){
        $this->set('title_for_layout','Environnements'); 
    }

    /**
     * liste les expressions de besoins
     * 
     * @param string $application
     * @param string $etat
     * @param string $type
     * @param string $perimetre
     * @throws UnauthorizedException
     */
    public function index($application=null,$etat=null,$type=null,$perimetre=null) {
        $this->set_title(); 
        if (isAuthorized('expressionbesoins', 'index')) :  
            $listentite = $this->get_visibility();
            $restriction = $this->get_restriction($listentite);              
            $getapplication = $this->get_environnement_application_filter($application);
            $getetat = $this->get_environnement_etat_filter($etat);
            $gettype = $this->get_environnement_type_filter($type);
            $getperimetre = $this->get_environnement_perimetre_filter($perimetre);
            $strfilter = $getapplication['filter'].$getetat['filter'].$gettype['filter'].$getperimetre['filter'];
            $newconditions = array($restriction,$getapplication['condition'],$getetat['condition'],$gettype['condition'],$getperimetre['condition']);
            $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions,'recursive'=>0));                
            $this->set('expressionbesoins', $this->paginate());
            $this->get_export($newconditions);
            $ObjApplications = new ApplicationsController();
            $ObjTypes = new TypesController();
            $ObjPerimetres = new PerimetresController();
            $ObjEtats = new EtatsController();
            $applications = $ObjApplications->get_list(1);
            $types = $ObjTypes->get_list(1);
            $perimetres = $ObjPerimetres->get_list(1);
            $etats = $ObjEtats->get_list(1);
            $this->set(compact('strfilter','applications','types','perimetres','etats'));    
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * ajoute une expression de besoin
     * 
     * @throws UnauthorizedException
     */
    public function add() {
        $this->set_title(); 
        if (isAuthorized('expressionbesoins', 'add')) : 
            if ($this->request->is('post')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Expressionbesoin->validate = array();
                    $this->History->goBack(1);
                else:           
                    $this->request->data['Expressionbesoin']['entite_id']=userAuth('entite_id');
                    $this->Expressionbesoin->create();
                    if ($this->Expressionbesoin->save($this->request->data)) {
                            $id = $this->Expressionbesoin->getLastInsertID();
                            $this->saveHistory($id);
                            $expb = $this->Expressionbesoin->findById($id);
                            $this->sendmailajout($expb);
                            $this->Session->setFlash(__('Expression du besoin sauvegardé',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Expression du besoin incorrect, veuillez corriger l\'expression du besoin',true),'flash_failure');
                    }
                endif;
            }
            $ObjApplications = new ApplicationsController();
            $ObjTypes = new TypesController();
            $ObjPerimetres = new PerimetresController();
            $ObjEtats = new EtatsController();
            $ObjComposants = new ComposantsController();
            $ObjLots = new LotsController();
            $ObjPhases = new PhasesController();	
            $ObjVolumetries = new VolumetriesController();
            $ObjPuissances = new PuissancesController();	
            $ObjArchitectures = new ArchitecturesController();
            $applications = $ObjApplications->get_select(1);
            $types = $ObjTypes->get_select(1);
            $perimetres = $ObjPerimetres->get_select(1);
            $etats = $ObjEtats->get_select(1);                
            $composants = $ObjComposants->get_select(1); 
            $lots = $ObjLots->get_select(1); 
            $phases = $ObjPhases->get_select(1); 
            $volumetries = $ObjVolumetries->get_select(1); 
            $puissances = $ObjPuissances->get_select(1); 
            $architectures = $ObjArchitectures->get_select(1); 
            $dsitenvs = array();
            $this->set(compact('applications', 'composants', 'perimetres', 'lots', 'etats', 'types', 'phases', 'volumetries', 'puissances', 'architectures','dsitenvs'));
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                  
    }

    /**
     * met à jour l'expression du besoin
     * 
     * @param string $id
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function edit($id = null) {
        $this->set_title();  
        if (isAuthorized('expressionbesoins', 'edit')) : 
            if (!$this->Expressionbesoin->exists($id)) {
                    throw new NotFoundException(__('Invalid expressionbesoin'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if (isset($this->params['data']['cancel'])) :
                    $this->Expressionbesoin->validate = array();
                    $this->History->goBack(1);
                else:                    
                    if ($this->Expressionbesoin->save($this->request->data)) {
                            $this->saveHistory($id);
                            $this->Session->setFlash(__('Expression du besoin modifée',true),'flash_success');
                            $this->History->goBack(1);
                    } else {
                            $this->Session->setFlash(__('Expression du besoin incorrect, veuillez corriger l\'expression du besoin',true),'flash_failure');
                    }
                endif;
            } else {
                    $options = array('conditions' => array('Expressionbesoin.' . $this->Expressionbesoin->primaryKey => $id));
                    $this->request->data = $this->Expressionbesoin->find('first', $options);
            }
            $ObjApplications = new ApplicationsController();
            $ObjTypes = new TypesController();
            $ObjPerimetres = new PerimetresController();
            $ObjEtats = new EtatsController();
            $ObjComposants = new ComposantsController();
            $ObjLots = new LotsController();
            $ObjPhases = new PhasesController();
            $ObjVolumetries = new VolumetriesController();
            $ObjPuissances = new PuissancesController();	
            $ObjArchitectures = new ArchitecturesController();
            $ObjHistoryexpbs = new HistoryexpbsController();
            $ObjDsitenvs = new DsitenvsController();
            $applications = $ObjApplications->get_select(1);
            $types = $ObjTypes->get_select(1);
            $perimetres = $ObjPerimetres->get_select(1);
            $etats = $ObjEtats->get_select(1);                
            $composants = $ObjComposants->get_select(1); 
            $lots = $ObjLots->get_select(1); 
            $phases = $ObjPhases->get_select(1); 
            $volumetries = $ObjVolumetries->get_select(1); 
            $puissances = $ObjPuissances->get_select(1); 
            $architectures = $ObjArchitectures->get_select(1);  
            $histories = $ObjHistoryexpbs->get_list($id);
            $dsitenvs = $ObjDsitenvs->get_select_for_application($this->request->data['Expressionbesoin']['application_id']);
            $this->set(compact('applications', 'composants', 'perimetres', 'lots', 'etats', 'types', 'phases', 'volumetries', 'puissances', 'architectures','histories','dsitenvs'));
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                  
    }

    /**
     * supprime l'expression du besoin
     * 
     * @param string $id
     * @param boolean $loop
     * @return boolean
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function delete($id = null,$loop=false) {
        $this->set_title();          
        if (isAuthorized('expressionbesoins', 'delete')) : 
            $this->Expressionbesoin->id = $id;
            if (!$this->Expressionbesoin->exists()) {
                    throw new NotFoundException(__('Expression du besoin incorrecte'));
            }
            $obj = $this->Expressionbesoin->find('first',array('conditions'=>array('Expressionbesoin.id'=>$id),'recursive'=>0));
            if($obj['Expressionbesoin']['ACTIF']==1):
                $newactif = $obj['Expressionbesoin']['ACTIF'] == 1 ? 0 : 1;
                $newetat = $newactif == 0 ? 4 : 1; //mise à jour du statut état qui est une donnée référentielle et qui peut donc être modifié !!!! pas top
                if ($this->Expressionbesoin->saveField('ACTIF',$newactif) && $this->Expressionbesoin->saveField('etat_id',$newetat)) {
                        $this->saveHistory($id);
                        if ($newactif==0):
                            $this->Session->setFlash(__('Expression du besoin suppriméee',true),'flash_success');
                            if($loop) : return true; endif;
                        else:
                            $this->Session->setFlash(__('Expression du besoin activée',true),'flash_success');
                            if($loop) : return true; endif;
                        endif;
                } else {
                        if ($newactif==0):
                            $this->Session->setFlash(__('Expression du besoin <b>NON</b> suppriméee',true),'flash_success');
                            if($loop) : return true; endif;
                        else:
                            $this->Session->setFlash(__('Expression du besoin <b>NON</b> activée',true),'flash_success');
                            if($loop) : return true; endif;
                        endif;                    
                }
                if(!$loop) : $this->History->notmove();  
                else:
                    return true;
                endif;
            else:
                if($this->Expressionbesoin->delete()):                      
                    $this->Session->setFlash(__('Expression du besoin suppriméee',true),'flash_success');
                    if(!$loop) : $this->History->goBack(1); 
                    else:
                        return true;
                    endif;
                else:
                    $this->Session->setFlash(__('Expression du besoin <b>NON</b> suppriméee',true),'flash_failure');
                    if($loop) : return false; endif;
                endif;
            endif;
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                  
    }

    /**
     * rends de façon dynamique l'expression du besoin active ou non
     * 
     * @param string $id
     * @return boolean
     */
    public function ajax_actif($id=null){
            $newid = $id == null ? $this->request->data('id') : $id;
            $result = false;                
            $this->Expressionbesoin->id = $newid;
            $obj = $this->Expressionbesoin->find('first',array('conditions'=>array('Expressionbesoin.id'=>$newid),'recursive'=>0));
            $newactif = $obj['Expressionbesoin']['ACTIF'] == 1 ? 0 : 1;
            $newetat = $newactif == 0 ? 4 : 1; //mise à jour du statut état qui est une donnée référentielle et qui peut donc être modifié !!!! pas top
            if ($this->Expressionbesoin->saveField('ACTIF',$newactif) && $this->Expressionbesoin->saveField('etat_id',$newetat)) {
                    $this->saveHistory($newid);
                    if ($id==null):
                        $this->Session->setFlash(__('Modification du statut actif pris en compte',true),'flash_success');
                    else:
                        $result = true;
                    endif;
            } else {
                    if ($id==null):
                       $this->Session->setFlash(__('Modification du statut actif <b>NON</b> pris en compte',true),'flash_failure');
                    else:
                        $result = false;
                    endif;                        
            }
            if ($id==null):
                exit();
            else:
                return $result;
            endif;
    } 

    /**
     * supprime toutes les expressions de besoin sélectionnés dans la vue
     * 
     * @param string $id
     * @return json
     */
    public function deleteall($id){
        $this->autoRender = false;
        if($this->request->data('id')!==''):
            $ids = explode('-', $this->request->data('id'));
            if(count($ids)>0 && $ids[0]!=""):
                foreach($ids as $newid):
                    if($this->delete($newid,true)):
                        echo $this->Session->setFlash(__('Modification du statut supprimé pris en compte pour toutes les expressions de besoin sélectionnées',true),'flash_success'); 
                    else :
                        $this->Session->setFlash(__('Modification du statut supprimé <b>NON</b> pris en compte pouter toutes les expressions de besoin sélectionnées',true),'flash_failure');
                    endif;
                endforeach; 
                sleep(3);
                //$this->History->goBack(1);
            endif;
        else :
            $this->Session->setFlash(__('Aucune expresion de besoin sélectionnée',true),'flash_failure');
        endif;
        return $this->request->data('id');        
    }         
        
    /**
     * duplique une expression de besoin
     * 
     * @param string $id
     * @throws UnauthorizedException
     */
    public function dupliquer($id = null) {
        if (isAuthorized('expressionbesoins', 'duplicate')) :
            $this->Expressionbesoin->id = $id;
            $record = $this->Expressionbesoin->read();
            unset($record['Expressionbesoin']['id']);
            unset($record['Expressionbesoin']['application_id']);  
            $record['Expressionbesoin']['application_id']=0;
            unset($record['Expressionbesoin']['etat_id']);  
            $record['Expressionbesoin']['etat_id']=0;                
            unset($record['Expressionbesoin']['COMMENTAIRE']);
            unset($record['Expressionbesoin']['DATELIVRAISON']);
            unset($record['Expressionbesoin']['DATEFIN']);
            unset($record['Expressionbesoin']['ACTIF']);
            unset($record['Expressionbesoin']['URL']);
            unset($record['Expressionbesoin']['URLLOGIN']);
            unset($record['Expressionbesoin']['URLPASSWORD']);                
            unset($record['Expressionbesoin']['created']);                
            unset($record['Expressionbesoin']['modified']);
            $this->Expressionbesoin->create();
            if ($this->Expressionbesoin->save($record)) {
                    $this->Session->setFlash(__('Expression du besoin dupliquée',true),'flash_success');
                    $this->redirect(array('action'=>'edit',$this->Expressionbesoin->getLastInsertID()));
            } else {
                    $this->Session->setFlash(__('Expression du besoin incorrecte, veuillez corriger l\'expression du besoin',true),'flash_failure');
            }               
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                
    } 

    /**
     * renvois un rapport (indicateur)
     * 
     * @throws UnauthorizedException
     */
    public function rapport(){
        "Rapport des ".strtolower($this->set_title());
        if (isAuthorized('expressionbesoins', 'rapports')) :  
            $ObjPerimetres = new PerimetresController();
            $ObjEtats = new EtatsController();
            $ObjLots = new LotsController();
            $etats = $ObjEtats->get_select(1);                
            $lots = $ObjLots->get_select(1);  
            $perimetres = $ObjPerimetres->get_select(1);  
            $mois = array('01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril','05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Août','09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
            $fiveyearago = date('Y')-5;
            for($i=0;$i<6;$i++):
                $year = $fiveyearago + $i;
                $annee[$year]=$year;
            endfor;                
            $this->set(compact('etats','lots','perimetres', 'mois', 'annee')); 
            if ($this->request->is('post')):
                $mois = $this->data['Expressionbesoin']['mois'];
                $annee = $this->data['Expressionbesoin']['annee'];                     
                $selectlot = $this->data['Expressionbesoin']['lot_id']=='' || $this->data['Expressionbesoin']['lot_id']=='4' ? '' : ' AND lot_id = '.$this->data['Expressionbesoin']['lot_id'];
                $selectperimetre = $this->data['Expressionbesoin']['perimetre_id']=='' ? '' : ' AND perimetre_id = '.$this->data['Expressionbesoin']['perimetre_id'];
                $thisetats = $this->data['Expressionbesoin']['etat_id'];
                $listetats = '';
                foreach($thisetats as $key => $value):
                    $listetats.=$value.',';
                endforeach;
                $selectetat = ' AND etat_id in ('.substr_replace($listetats ,"",-1).')';
                $sql = "select count(expressionbesoins.id) as NB,MONTH(DATELIVRAISON) as MOIS, lots.NOM as LOT,applications.NOM as APPLICATION,etats.NOM as ETAT, perimetres.`NOM` AS PERIMETRE
                        from expressionbesoins
                        LEFT JOIN lots on expressionbesoins.lot_id = lots.id
                        LEFT JOIN applications on expressionbesoins.application_id = applications.id
                        LEFT JOIN perimetres on expressionbesoins.perimetre_id = perimetres.id
                        LEFT JOIN etats on expressionbesoins.etat_id = etats.id
                        WHERE (DATELIVRAISON IS NOT NULL AND  DATELIVRAISON <> '0000-00-00' AND MONTH(DATELIVRAISON) = ".$mois.
                        ") AND YEAR(DATELIVRAISON) = ".$annee." ".$selectlot.$selectperimetre.$selectetat.
                        " group by lot_id, application_id, perimetre_id,etat_id
                        order by MONTH(DATELIVRAISON) asc,lot_id asc, perimetre_id asc, application_id asc, etat_id asc;";
                $results = $this->Expressionbesoin->query($sql);
                $this->set('results',$results);
                $chartsql = "select count(expressionbesoins.id) as NB, lots.NOM as LOT, perimetres.`NOM` AS PERIMETRE
                        from expressionbesoins
                        LEFT JOIN lots on expressionbesoins.lot_id = lots.id
                        LEFT JOIN perimetres on expressionbesoins.perimetre_id = perimetres.id
                        WHERE (DATELIVRAISON IS NOT NULL AND  DATELIVRAISON <> '0000-00-00' AND MONTH(DATELIVRAISON) = ".$mois.
                        ") AND YEAR(DATELIVRAISON) = ".$annee." ".$selectlot.$selectperimetre.$selectetat.
                        " group by lot_id, perimetre_id
                        order by lot_id asc, perimetre_id asc;";
                $chartresults = $this->Expressionbesoin->query($chartsql);
                /**
                 * retravailler le résultat pour mettre des zéro si plusieurs lots et applications différentes
                 */
                $appresultsql= "select perimetres.NOM
                        from expressionbesoins
                        LEFT JOIN perimetres on expressionbesoins.perimetre_id = perimetres.id
                        WHERE (DATELIVRAISON IS NOT NULL AND  DATELIVRAISON <> '0000-00-00' AND MONTH(DATELIVRAISON) = ".$mois.
                        ") AND YEAR(DATELIVRAISON) = ".$annee." ".$selectlot.$selectperimetre.$selectetat.
                        " group by perimetre_id
                        order by perimetre_id asc;";
                $appresult = $this->Expressionbesoin->query($appresultsql);
                $lotresultsql= "select lots.NOM
                        from expressionbesoins
                        LEFT JOIN lots on expressionbesoins.lot_id = lots.id
                        WHERE (DATELIVRAISON IS NOT NULL AND  DATELIVRAISON <> '0000-00-00' AND MONTH(DATELIVRAISON) = ".$mois.
                        ") AND YEAR(DATELIVRAISON) = ".$annee." ".$selectlot.$selectperimetre.$selectetat.
                        " group by lot_id
                        order by lot_id asc;";
                $lotresult = $this->Expressionbesoin->query($lotresultsql);                    
                if(count($lotresult)>1):
                    $i = 0;
                    $array = array();
                    foreach($chartresults as $result):
                            $array[]=array($result['lots']['LOT'],$result['perimetres']['PERIMETRE']);
                    endforeach;
                    foreach($lotresult as $lot):
                        foreach($appresult as $app):
                            $completearray[]=array($lot['lots']['NOM'],$app['perimetres']['NOM']);
                        endforeach;
                        $i++;
                    endforeach;
                    $diff = narray_diff ($array,$completearray);
                    foreach($diff as $result):
                        $add[]=array(array('NB' => '0'),'lots' => array('LOT' => $result[0]),'perimetres' => array('PERIMETRE' => $result[1]));
                    endforeach;
                    if(isset($add) && is_array($add)):
                    $chartresults = array_merge($chartresults,$add);
                    else:
                        $chartresults = $chartresults;
                    endif;
                endif;                    
                $this->set('chartresults',$chartresults); 
                /**
                 * Calcul du nombre d'environnements dans un état a valider, validé et livré sur tous les environnements
                 */
                $chartcumulenv = "SELECT COUNT(expressionbesoins.id) AS NB, lots.NOM AS LOT, perimetres.NOM AS PERIMETRE
                                  FROM expressionbesoins
                                  LEFT JOIN lots ON expressionbesoins.lot_id = lots.id
                                  LEFT JOIN perimetres ON expressionbesoins.`perimetre_id` = perimetres.id
                                  WHERE `etat_id` IN (3) 
                                  GROUP BY perimetres.NOM,lots.NOM
                                  order by perimetre_id asc;"; //retiré les états 1 et 2 pour ne prendre que les livrés
                $chartcumulenvresults = $this->Expressionbesoin->query($chartcumulenv);
                $this->set('chartcumulenvresults',$chartcumulenvresults);
                $charthistosql = "select count(expressionbesoins.id) as NB,CONCAT(IF(MONTH(DATELIVRAISON)<10,CONCAT('0',MONTH(DATELIVRAISON)),MONTH(DATELIVRAISON)),'/',YEAR(DATELIVRAISON)) AS MOIS, lots.NOM as LOT
                        from expressionbesoins
                        LEFT JOIN lots on expressionbesoins.lot_id = lots.id
                        WHERE (DATELIVRAISON IS NOT NULL AND  DATELIVRAISON <> '0000-00-00'".
                        ") ".$selectlot.$selectperimetre.$selectetat.
                        " group by CONCAT(YEAR(DATELIVRAISON),IF(MONTH(DATELIVRAISON)<10,CONCAT('0',MONTH(DATELIVRAISON)),MONTH(DATELIVRAISON))),lot_id
                        order by CONCAT(YEAR(DATELIVRAISON),IF(MONTH(DATELIVRAISON)<10,CONCAT('0',MONTH(DATELIVRAISON)),MONTH(DATELIVRAISON))) asc,lot_id asc;";
                $charthistoresults = $this->Expressionbesoin->query($charthistosql);
                $this->set('charthistoresults',$charthistoresults);                    
            endif;                
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;                 
    }

    /**
     * recherche des expression de besoins
     * 
     * @param string $application
     * @param string $etat
     * @param string $type
     * @param string $perimetre
     * @param string $keywords
     * @throws UnauthorizedException
     */
    public function search($application=null,$etat=null,$type=null,$perimetre=null,$keywords=null){
        $this->set_title();             
        if (isAuthorized('expressionbesoins', 'index')) :
            if(isset($this->params->data['Expressionbesoin']['SEARCH'])):
                $keywords = $this->params->data['Expressionbesoin']['SEARCH'];
            elseif (isset($keywords)):
                $keywords=$keywords;
            else:
                $keywords=''; 
            endif;
            $this->set('keywords',$keywords);
            if($keywords!= ''):
                $arkeywords = explode(' ',trim($keywords));  
                $listentite = $this->get_visibility();
                $restriction = $this->get_restriction($listentite);              
                $getapplication = $this->get_environnement_application_filter($application);
                $getetat = $this->get_environnement_etat_filter($etat);
                $gettype = $this->get_environnement_type_filter($type);
                $getperimetre = $this->get_environnement_perimetre_filter($perimetre);
                $strfilter = $getapplication['filter'].$getetat['filter'].$gettype['filter'].$getperimetre['filter'];
                $newconditions = array($restriction,$getapplication['condition'],$getetat['condition'],$gettype['condition'],$getperimetre['condition']);
                foreach ($arkeywords as $key=>$value):
                    $ornewconditions[] = array('OR'=>array("Application.NOM LIKE '%".$value."%'","Composant.NOM LIKE '%".$value."%'","Perimetre.NOM LIKE '%".$value."%'","Lot.NOM LIKE '%".$value."%'","Etat.NOM LIKE '%".$value."%'","Type.NOM LIKE '%".$value."%'","Phase.NOM LIKE '%".$value."%'","Volumetrie.NOM LIKE '%".$value."%'","Puissance.NOM LIKE '%".$value."%'","Architecture.NOM LIKE '%".$value."%'","Dsitenv.NOM LIKE '%".$value."%'","Expressionbesoin.USAGE LIKE '%".$value."%'","Expressionbesoin.NOMUSAGE LIKE '%".$value."%'","Expressionbesoin.PVU LIKE '%".$value."%'"));
                endforeach;
                $conditions = array($newconditions,'OR'=>$ornewconditions);
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$conditions,'recursive'=>0));  
                $this->set('expressionbesoins', $this->paginate());
                $this->get_export($newconditions);
                $ObjApplications = new ApplicationsController();
                $ObjTypes = new TypesController();
                $ObjPerimetres = new PerimetresController();
                $ObjEtats = new EtatsController();
                $applications = $ObjApplications->get_list(1);
                $types = $ObjTypes->get_list(1);
                $perimetres = $ObjPerimetres->get_list(1);
                $etats = $ObjEtats->get_list(1);
                $this->set(compact('strfilter','applications','types','perimetres','etats')); 
            else:
                $this->redirect(array('action'=>'index',$application,$etat,$type,$perimetre));
            endif; 
        else :
            $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;  
    }  

    /**
     * exporte au format Excel les informations contrenu dans le cache
     */
    function export_xls() {
        //set_time_limit(90);
        $this->autoRender = false;
        $data = $this->Session->read('xls_export');             
        $this->set('rows',$data);
        $this->render('export_xls','export_xls');
    }   

    /**
     * enregistre l'historique lors du'ne modification de l'expression de besoins
     * 
     * @param string $id
     * @param string $msg
     * @throws UnauthorizedException
     */
    public function saveHistory($id=null,$msg=null){
        if($id!=null && userAuth('id')!=null):
            $msg = $msg==null ? true : false;
            $this->Expressionbesoin->id = $id;
            $obj = $this->Expressionbesoin->read(); 
            $record['Historyexpb']['expressionbesoins_id']=$id;
            $record['Historyexpb']['etat_id']=$obj['Expressionbesoin']['etat_id'];
            $record['Historyexpb']['DATEFIN']=  CUSDate($obj['Expressionbesoin']['DATEFIN']);
            $record['Historyexpb']['DATELIVRAISON']=CUSDate($obj['Expressionbesoin']['DATELIVRAISON']);
            $record['Historyexpb']['MODIFIEDBY']= userAuth('id'); 
            $record['Historyexpb']['created']=date('Y-m-d H:i:s');
            $record['Historyexpb']['modified']=date('Y-m-d H:i:s');
            $this->Expressionbesoin->Historyexpb->create();
            if ($this->Expressionbesoin->Historyexpb->save($record)) {
                    if ($msg) : $this->Session->setFlash(__('Expression du besoin historisée',true),'flash_success'); endif;
            } else {
                    if ($msg) : $this->Session->setFlash(__('Historisation de l\'expression du besoin incorrecte, veuillez corriger l\'expression du besoin',true),'flash_failure'); endif;
            }
        else:
            $this->Session->setFlash(__('Historisation impossible l\'expression du besoin est incorecte.',true),'flash_warning');
            throw new UnauthorizedException("Vous n'êtes pas autorisé à utiliser cette fonctionnalité de l'outil");
        endif;
    }    

    /**
     * import au format CSV à partir du fichier mis à disposition des expressions de besoins
     */
    public function importCSV(){            
        $file = isset($this->data['Expressionbesoin']['file']['name']) ? $this->data['Expressionbesoin']['file']['name'] : '';
        $file_type = strrchr($file,'.');
        $completpath = WWW_ROOT.'files/upload/';
        if($this->data['Expressionbesoin']['file']['tmp_name']!='' && $file_type=='.csv'):
            $filename = $completpath.$this->data['Expressionbesoin']['file']['name'];
            move_uploaded_file($this->data['Expressionbesoin']['file']['tmp_name'],$filename);               
            $messages = $this->Expressionbesoin->importCSV($this->data['Expressionbesoin']['file']['name']);
            $allmsg = "Importation prise en compte, résultat ci-dessous :<ul>";
            foreach($messages as $message):
                $x = 0;
                foreach ($message as $msg):                    
                $thismsg = !empty($msg) ? $msg : '';
                $x++;
                $allmsg .= "<li>".$thismsg."</li>";
                endforeach;
            endforeach;
            $allmsg .= "</ul>";
            $this->Session->setFlash(__($allmsg,true),'flash_success');
        else :
            $this->Session->setFlash(__('Fichier <b>NON</b> reconnu',true),'flash_failure');
        endif;            
        $this->History->notmove();
    }   

    /**
     * envois d'un mail sur l'ajout d'une expression de besoin
     * 
     * @param array $expb
     */
    public function sendmailajout($expb){
        $ObjParameters = new ParametersController();
        $valideurs = $ObjParameters->get_gestionnaireenvironnement();
        $to = explode(';', $valideurs['Parameter']['param']);
        $from = userAuth('MAIL');
        $objet = 'SAILL : Nouvelle demande d\'environnement ['.$expb['Application']['NOM'].']';
        $message = "Merci de traiter la demande suivnate: ".
                '<ul>
                <li>Application :'.$expb['Application']['NOM'].'</li>
                <li>Composant :'.$expb['Composant']['NOM'].'</li>
                <li>Périmètre :'.$expb['Perimetre']['NOM'].'</li> 
                <li>Lot :'.$expb['Lot']['NOM'].'</li>       
                </ul>';
        if(count($to) > 0):
            try{
            $email = new CakeEmail();
            $email->config('smtp')
                    ->emailFormat('html')
                    ->from($from)
                    ->to($to)
                    ->subject($objet)
                    ->send($message);
            }
            catch(Exception $e){
                $this->Session->setFlash(__('Erreur lors de l\'envois du mail - '.translateMailException($e->getMessage()),true),'flash_warning');
            }  
        endif;
    }         
}
