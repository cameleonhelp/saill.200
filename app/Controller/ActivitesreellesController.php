<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Activitesreelles Controller
 *
 * @property Activitesreelle $Activitesreelle
 */
class ActivitesreellesController extends AppController {
        public $components = array('History');    
        public $paginate = array(
        'order' => array('Activitesreelle.utilisateur_id' => 'asc','Activitesreelle.DATE' => 'desc'),
        );
        
/**
 * index method
 *
 * @return void
 */
	public function index($etat=null,$utilisateur=null,$mois=null,$annee=null,$indisponibilite=null) {
            //$this->Session->delete('history');
            if (isAuthorized('activitesreelles', 'index')) :
                switch ($etat){
                    case 'tous':
                        $newconditions[]="1=1";
                        $fetat = "toutes les feuilles de temps";
                        break;
                    case 'actif':
                    case '<':    
                    case null:
                        $newconditions[]="Activitesreelle.VEROUILLE = 1";
                        $fetat = "toutes les feuilles de temps actives";
                        break;                    
                    case 'facture':
                        $newconditions[]="Activitesreelle.VEROUILLE = 0";
                        $fetat = "toutes les feuilles de temps facturées";
                        break;                      
                }  
                $this->set('fetat',$fetat); 
                if (areaIsVisible() || $utilisateur==userAuth('id')):
                switch ($utilisateur){
                    case 'tous':
                        $newconditions[]="1=1";
                        $futilisateur = "tous les utilisateurs";
                        break;
                    case null:
                        $newconditions[]="Activitesreelle.utilisateur_id = ".userAuth('id');
                        $this->Activitesreelle->Utilisateur->recursive = -1;
                        $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>userAuth('id'))));
                        $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];
                        break;                     
                    default:
                        $newconditions[]="Activitesreelle.utilisateur_id = ".$utilisateur;
                        $this->Activitesreelle->Utilisateur->recursive = -1;
                        $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>$utilisateur)));
                        $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];
                        break;                      
                }  
                else:
                    $newconditions[]="Activitesreelle.utilisateur_id = ".userAuth('id');
                    $this->Activitesreelle->Utilisateur->recursive = -1;
                    $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>userAuth('id'))));
                    $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];                 
                endif;                
                $this->set('futilisateur',$futilisateur);
                $annee = $annee==null ? date('Y') : $annee;
                switch ($mois){
                    case 'tous':
                    case null:
                        $newconditions[]="1=1";
                        $fperiode = "";
                        break;
                    default:
                        $dernierjour = date('t', mktime(0, 0, 0, $mois, 5, $annee));
                        $debut = $annee."-".$mois."-01";
                        $datedebut = startWeek($debut);
                        $datefin = $annee."-".$mois."-".$dernierjour;
                        $newconditions[]="Activitesreelle.DATE BETWEEN '".$datedebut."' AND '".$datefin."'";
                        $moiscal = array('01'=>"janvier",'02'=>"février",'03'=>"mars",'04'=>"avril",'05'=>"mai",'06'=>"juin",'07'=>"juillet",'08'=>"août",'09'=>"septembre",'10'=>"octobre",'11'=>"novembre",'12'=>"décembre",);
                        $fperiode = "pour le mois de ".$moiscal[$mois]." ".$annee;
                        break;                      
                }  
                $indisponibilite = $indisponibilite==null ? 0 : $indisponibilite;
                switch ($indisponibilite){
                    case '1':
                        $newconditions[]="Activite.projet_id!=1";
                        break;
                    default:
                        $newconditions[]="1=1";
                        break;                      
                }  
                $this->set('fperiode',$fperiode);                
                $this->set('title_for_layout','Feuilles de temps');
                $this->Activitesreelle->Utilisateur->recursive = -1;
                $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id > 1','Utilisateur.ACTIF'=>1,'OR'=>array('Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id'=>-1)),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
                $this->set('utilisateurs',$utilisateurs);
                $icsutilisateurs = $this->Activitesreelle->Utilisateur->find('list',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id > 1','Utilisateur.ACTIF'=>1,'OR'=>array('Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id'=>-1)),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
                $this->set('icsutilisateurs',$icsutilisateurs); 
                $annee = $this->Activitesreelle->find('all',array('fields'=>array('YEAR(Activitesreelle.DATE) AS ANNEE'),'group'=>array('YEAR(Activitesreelle.DATE)'),'order'=>array('YEAR(Activitesreelle.DATE)' => 'asc')));
                $this->set('annees',$annee);                  
                $this->Activitesreelle->recursive = 1;
                $group = $this->Activitesreelle->find('all',array('fields'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id','Utilisateur.username','Utilisateur.NOM','Utilisateur.PRENOM','COUNT(Activitesreelle.DATE) AS NBACTIVITE'),'group'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id'),'order'=>array('Activitesreelle.utilisateur_id' => 'asc','Activitesreelle.DATE' => 'desc' ),'conditions'=>$newconditions));
                $this->set('groups',$group);
                $this->paginate = array('limit'=>$this->Activitesreelle->find('count'));
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions));                 
		$this->Activitesreelle->recursive = 0;
                $activitesreeelles = $this->Activitesreelle->find('all',$this->paginate);
		$this->set('activitesreelles', $activitesreeelles);
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();            
            endif;                
	}

 /**
 * afacturer method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function afacturer($etat=null,$utilisateur=null,$mois=null,$annee=null) {
            if (isAuthorized('activitesreelles', 'index')) :
                $newconditions[]="Activitesreelle.facturation_id IS NULL";                
                switch ($etat){                  
                    case 'facture':
                    case '<':
                    case null:
                        $newconditions[]="Activitesreelle.VEROUILLE = 0";
                        $fetat = "toutes les feuilles de temps à facturer";
                        break;                      
                }  
                $this->set('fetat',$fetat); 
                if (areaIsVisible() || $utilisateur==userAuth('id')):
                switch ($utilisateur){
                    case 'tous':
                        $newconditions[]="1=1";
                        $futilisateur = "tous les utilisateurs";
                        break;
                    case null:
                        $newconditions[]="Activitesreelle.utilisateur_id = ".userAuth('id');
                        $this->Activitesreelle->Utilisateur->recursive = -1;
                        $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>userAuth('id'),'Utilisateur.GESTIONABSENCES'=>1)));
                        $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];
                        break;                     
                    default:
                        $newconditions[]="Activitesreelle.utilisateur_id = ".$utilisateur;
                        $this->Activitesreelle->Utilisateur->recursive = -1;
                        $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>$utilisateur)));
                        $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];
                        break;                      
                }  
                else:
                    $newconditions[]="Activitesreelle.utilisateur_id = ".userAuth('id');
                    $this->Activitesreelle->Utilisateur->recursive = -1;
                    $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id'=>userAuth('id'))));
                    $futilisateur = $utilisateur['Utilisateur']['NOMLONG'];                 
                endif;                
                $this->set('futilisateur',$futilisateur);
                $annee = $annee==null ? date('Y') : $annee;
                switch ($mois){
                    case 'tous':
                        $newconditions[]="1=1";
                        $fperiode = "";
                        break;
                    case null:
                        $mois=date('m');
                        $dernierjour = date('t', mktime(0, 0, 0, $mois, 5, $annee));
                        $datedebut = $annee."-".$mois."-01";
                        $datefin = $annee."-".$mois."-".$dernierjour;
                        $newconditions[]="Activitesreelle.DATE BETWEEN '".$datedebut."' AND '".$datefin."'";
                        $moiscal = array('01'=>"janvier",'02'=>"février",'03'=>"mars",'04'=>"avril",'05'=>"mai",'06'=>"juin",'07'=>"juillet",'08'=>"août",'09'=>"septembre",'10'=>"octobre",'11'=>"novembre",'12'=>"décembre",);
                        $fperiode = "pour le mois de ".$moiscal[$mois]." ".$annee;
                        break;                        
                    default:
                        $dernierjour = date('t', mktime(0, 0, 0, $mois, 5, $annee));
                        $datedebut = $annee."-".$mois."-01";
                        $datefin = $annee."-".$mois."-".$dernierjour;
                        $newconditions[]="Activitesreelle.DATE BETWEEN '".$datedebut."' AND '".$datefin."'";
                        $moiscal = array('01'=>"janvier",'02'=>"février",'03'=>"mars",'04'=>"avril",'05'=>"mai",'06'=>"juin",'07'=>"juillet",'08'=>"août",'09'=>"septembre",'10'=>"octobre",'11'=>"novembre",'12'=>"décembre",);
                        $fperiode = "pour le mois de ".$moiscal[$mois]." ".$annee;
                        break;                      
                }  
                $this->set('fperiode',$fperiode);                
                $this->set('title_for_layout','Feuilles de temps à facturer');
                $this->Activitesreelle->Utilisateur->recursive = -1;
                $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id > 1','Utilisateur.ACTIF'=>1,'OR'=>array('Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id'=>-1)),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
                $this->set('utilisateurs',$utilisateurs);
                $this->Activitesreelle->recursive = 1;
                $group = $this->Activitesreelle->find('all',array('fields'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id','Utilisateur.username','Utilisateur.NOM','Utilisateur.PRENOM','COUNT(Activitesreelle.DATE) AS NBACTIVITE'),'group'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id'),'order'=>array('Activitesreelle.utilisateur_id' => 'asc','Activitesreelle.DATE' => 'desc' ),'conditions'=>$newconditions));
                $this->set('groups',$group); 
                $annee = $this->Activitesreelle->find('all',array('fields'=>array('YEAR(Activitesreelle.DATE) AS ANNEE'),'group'=>array('YEAR(Activitesreelle.DATE)'),'order'=>array('YEAR(Activitesreelle.DATE)' => 'asc')));
                $this->set('annees',$annee);                 
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions));                 
		$this->Activitesreelle->recursive = 0;
                $activitesreeelles = $this->Activitesreelle->find('all',$this->paginate);
		$this->set('activitesreelles', $activitesreeelles);
                $this->Activitesreelle->recursive = 0;
                $export = $this->Activitesreelle->find('all',array('conditions'=>$newconditions,'order' => array('Activitesreelle.DATE' => 'asc')));
                $this->Session->write('xls_export',$export);                 
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();            
            endif;                
        }
        
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
            if (isAuthorized('activitesreelles', 'view')) :
                $this->set('title_for_layout','Feuilles de temps');            
		if (!$this->Activitesreelle->exists($id)) {
			throw new NotFoundException(__('Feuille de temps incorrecte'));
		}
		$options = array('conditions' => array('Activitesreelle.' . $this->Activitesreelle->primaryKey => $id));
		$this->set('activitesreelle', $this->Activitesreelle->find('first', $options));
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();          
            endif;                
	}

/**
 * add method
 *
 * @return void
 */
	public function add($utilisateur_id=null,$date=null,$action_id=null) {
            if (isAuthorized('activitesreelles', 'add')) :
                $this->set('title_for_layout','Feuilles de temps');            
		if ($this->request->is('post')) {
                $activitesreelles = $this->request->data['Activitesreelle'];  
                foreach($activitesreelles as $activitesreelle):
                    if (is_array($activitesreelle) && $activitesreelle['activite_id'] != '' && $this->isUnique($activitesreelle['utilisateur_id'], $activitesreelle['activite_id'], $activitesreelle['domaine_id'],$date)):
                        $this->Activitesreelle->create();
                        if ($this->Activitesreelle->save($activitesreelle)):
                            $this->Session->setFlash(__('La feuille de temps est sauvegardée'),'default',array('class'=>'alert alert-success'));
                            $projet = $this->Activitesreelle->Activite->find('first',array('fields'=>array('projet_id'),'conditions'=>array('id'=>$activitesreelle['activite_id']),'recursive'=>-1));
                            if ($projet['Activite']['projet_id']==1):
                                    $this->sendmailabsences($activitesreelle);
                            endif;
                        else :
                            $this->Session->setFlash(__('La feuille de temps est incorrecte, veuillez corriger la feuille de temps'),'default',array('class'=>'alert alert-error'));
                        endif;   
                    endif;
                endforeach; 
                $this->History->goBack(1); 
                }
                $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('conditions'=>array('id'=>$utilisateur_id),'recursive'=>-1));
                $this->set('utilisateur',$utilisateur);
                $this->Activitesreelle->Activite->recursive = 0;
                $activites = $this->Activitesreelle->Activite->find('all',array('fields'=>array('id','Activite.NOM','Projet.NOM'),'order'=>array('Projet.NOM'=>'asc','Activite.NOM'=>'asc')));
		$this->set('activites', $activites);  
                $domaines = $this->Activitesreelle->Domaine->find('list',array('fields'=>array('id','NOM'),'order'=>array('Domaine.NOM')));
                $this->set('domaines',$domaines);                 
                if ($action_id != null && $action_id != '<') :
                    $this->Activitesreelle->Action->recursive = -1;
                    $action = $this->Activitesreelle->Action->find('first',array('conditions'=>array('Action.id'=>$action_id)));
                    $this->request->data['Activitesreelle']['utilisateur_id'] = $action['Action']['utilisateur_id'];
                    $this->request->data['Activitesreelle']['DATE'] = $action['Action']['DEBUT'];
                    $this->request->data['Activitesreelle']['activite_id'] = $action['Action']['activite_id'];
                endif;
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();            
            endif;                
        }

/**
 * newactivite method
 * 
 * liste des utilisateurs pour ensuite renvoyer vers la méthode add
 */        
        public function newactivite(){
            if (isAuthorized('activitesreelles', 'add')) :
                $this->set('title_for_layout','Feuilles de temps');            
		if ($this->request->is('post')) {
                    $this->redirect(array('action' => 'add',$this->data['Activitesreelle']['utilisateur_id'],  CUSDate($this->data['Activitesreelle']['DATE'])));
		}
                $this->Activitesreelle->Utilisateur->recursive = -1;
		$utilisateurs = $this->Activitesreelle->Utilisateur->find('list',array('fields'=>array('id','NOMLONG'),'conditions'=>array('Utilisateur.ACTIF'=>1,'Utilisateur.id>1','OR'=>array('Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id'=>-1)),'order'=>array('Utilisateur.NOMLONG'=>'asc')));
		$this->set('utilisateurs', $utilisateurs);
                $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('fields'=>array('id','NOMLONG'),'conditions'=>array('Utilisateur.id'=>  userAuth('id')),'order'=>array('Utilisateur.NOMLONG'=>'asc'),'recursive'=>-1));           
                $this->set('utilisateur', $utilisateur);
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();            
            endif;                           
        }
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
            if (isAuthorized('activitesreelles', 'edit')) :
                $this->set('title_for_layout','Feuilles de temps');            
		if (!$this->Activitesreelle->exists($id)) {
			throw new NotFoundException(__('Feuille de temps incorrecte'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
                    $activitesreelles = $this->request->data['Activitesreelle'];  
                    foreach($activitesreelles as $activitesreelle):
                        if (is_array($activitesreelle) && isset($activitesreelle['id']) && $activitesreelle['activite_id'] != '' && $this->isUnique($activitesreelle['utilisateur_id'], $activitesreelle['activite_id'], $activitesreelle['domaine_id'],$activitesreelle['DATE'])):
                            //$this->Activitesreelle->create();
                            if ($this->Activitesreelle->save($activitesreelle)):
                                $this->Session->setFlash(__('La feuille de temps est sauvegardée'),'default',array('class'=>'alert alert-success'));
                                $projet = $this->Activitesreelle->Activite->find('first',array('fields'=>array('projet_id'),'conditions'=>array('id'=>$activitesreelle['activite_id']),'recursive'=>-1));
                                if ($projet['Activite']['projet_id']==1):
                                    $this->sendmailabsences($activitesreelle);
                                endif;
                            else :
                                $this->Session->setFlash(__('La feuille de temps est incorrecte, veuillez corriger la feuille de temps'),'default',array('class'=>'alert alert-error'));
                            endif; 
                        elseif (is_array($activitesreelle) && !isset($activitesreelle['id']) && $activitesreelle['activite_id'] != ''):
                            $this->Activitesreelle->create();
                            if ($this->Activitesreelle->save($activitesreelle)):
                                $this->Session->setFlash(__('La feuille de temps est sauvegardée'),'default',array('class'=>'alert alert-success'));
                                
                            else :
                                $this->Session->setFlash(__('La feuille de temps est incorrecte, veuillez corriger la feuille de temps'),'default',array('class'=>'alert alert-error'));
                            endif; 
                        endif;
                    endforeach; 
                    $this->History->goBack(1); 
		} else {
                    $date = $this->Activitesreelle->find('first',array('fields'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id'),'conditions'=>array('Activitesreelle.id'=>$id),'recursive'=>-1));
                    $activitesreelles = $this->Activitesreelle->find('all',array('conditions'=>array('Activitesreelle.utilisateur_id'=>$date['Activitesreelle']['utilisateur_id'],'Activitesreelle.DATE'=>CUSDate($date['Activitesreelle']['DATE'])),'recursive'=>-1));
                    $this->set('activitesreelles', $activitesreelles);
                    $domaines = $this->Activitesreelle->Domaine->find('list',array('fields'=>array('id','NOM'),'order'=>array('Domaine.NOM')));
                    $this->set('domaines',$domaines);  
                    $utilisateur = $this->Activitesreelle->Utilisateur->find('first',array('conditions'=>array('id'=>$date['Activitesreelle']['utilisateur_id']),'recursive'=>-1));
                    $this->set('utilisateur',$utilisateur);                    
                    $activites = $this->Activitesreelle->Activite->find('all',array('fields'=>array('id','Activite.NOM','Projet.NOM'),'order'=>array('Projet.NOM'=>'asc','Activite.NOM'=>'asc')));
                    $this->set('activites', $activites);
		}
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();       
            endif;                
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null,$loop = null) {
	    $loop = $loop==null ? false : $loop;
            if (isAuthorized('activitesreelles', 'delete')) :
                $this->set('title_for_layout','Feuilles de temps');            
		$this->Activitesreelle->id = $id;
		if (!$this->Activitesreelle->exists()) {
			throw new NotFoundException(__('Feuille de temps incorrecte'));
		}
		$activitesreelles = $this->Activitesreelle->find('first',array('conditions'=>array('Activitesreelle.id'=>$id)));
                if ($activitesreelles['Activitesreelle']['VEROUILLE']==1):
                    if ($this->Activitesreelle->delete()) {
                        if(!$loop):
                        $this->Session->setFlash(__('Feuille de temps supprimée'),'default',array('class'=>'alert alert-success'));
                        $this->History->goBack();
                        endif;
                    }
                endif;
                if(!$loop):
                $this->Session->setFlash(__('Feuille de temps <b>NON</b> supprimée'),'default',array('class'=>'alert alert-error'));
                $this->History->goBack();
                endif;
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();        
            endif;                
	}
        
/**
 * duplicate method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function duplicate($id = null) {
            if (isAuthorized('activitesreelles', 'duplicate')) :
                $this->set('title_for_layout','Feuilles de temps');  
                $this->Activitesreelle->id = $id;
                $record = $this->Activitesreelle->read();
                $date = $record['Activitesreelle']['DATE'];
                unset($record['Activitesreelle']['id']);
                unset($record['Activitesreelle']['DATE']);
                unset($record['Activitesreelle']['created']);                
                unset($record['Activitesreelle']['modified']);
                $date = new DateTime($this->Activitesreelle->CUSDate($date));
                $date->add(new DateInterval('P7D'));                
                $record['Activitesreelle']['DATE'] = $date->format('d/m/Y');
                if ($this->ActiviteExists($record['Activitesreelle']['utilisateur_id'], $record['Activitesreelle']['DATE'], $record['Activitesreelle']['activite_id']) > 0){
                    $this->Session->setFlash(__('Feuille de temps existante'),'default',array('class'=>'alert alert-info'));
                    $this->redirect(array('action' => 'edit',$this->ActiviteExists($record['Activitesreelle']['utilisateur_id'], $record['Activitesreelle']['DATE'], $record['Activitesreelle']['activite_id'])));
                }                
                $this->Activitesreelle->create();
                if ($this->Activitesreelle->save($record)) {
                    $this->Session->setFlash(__('Feuille de temps dupliquée'),'default',array('class'=>'alert alert-success'));
                    $lastid = $this->Activitesreelle->getLastInsertID();
                    $projet = $this->Activitesreelle->Activite->find('first',array('fields'=>array('projet_id'),'conditions'=>array('id'=>$lastid),'recursive'=>-1));
                    if ($projet['Activite']['projet_id']==1):
                            $this->sendmailabsences($record);
                    endif;                        
                        $this->redirect(array('action' => 'edit',$this->Activitesreelle->getLastInsertID()));
                } 
		$this->Session->setFlash(__('Feuille de temps <b>NON</b> dupliqué'),'default',array('class'=>'alert alert-error'));    
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();
            endif;                
	}  
        
/**
 * autoduplicate method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function autoduplicate($id = null) {
            if (isAuthorized('activitesreelles', 'update')) :
                $this->set('title_for_layout','Feuilles de temps');  
                $this->Activitesreelle->id = $id;
                $record = $this->Activitesreelle->read();
                $date = $record['Activitesreelle']['DATE'];
                unset($record['Activitesreelle']['id']);
                unset($record['Activitesreelle']['DATE']);
                unset($record['Activitesreelle']['created']);                
                unset($record['Activitesreelle']['modified']);
                $date = new DateTime($this->Activitesreelle->CUSDate($date));
                $date->add(new DateInterval('P7D'));                
                $record['Activitesreelle']['DATE'] = $date->format('d/m/Y');
                if ($this->ActiviteExists($record['Activitesreelle']['utilisateur_id'], $record['Activitesreelle']['DATE'], $record['Activitesreelle']['activite_id']) > 0){
                    $this->Session->setFlash(__('Feuille de temps existante'),'default',array('class'=>'alert alert-info'));
                    $this->History->goBack();
                }                
                $this->Activitesreelle->create();
                if ($this->Activitesreelle->save($record)) {
                    $this->Session->setFlash(__('Feuille de temps dupliquée'),'default',array('class'=>'alert alert-success'));
                    $this->History->goBack();
                } 
		$this->Session->setFlash(__('Feuille de temps <b>NON</b> dupliqué'),'default',array('class'=>'alert alert-error'));  
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();        
            endif;                
	}   
        
/**
 * updatefacturation method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function updatefacturation($id = null,$loop=false) {
            if (isAuthorized('activitesreelles', 'update')) :
                $this->set('title_for_layout','Feuilles de temps');  
                $this->Activitesreelle->id = $id;
                $record = $this->Activitesreelle->read();
                //TODO : [JLR] à voir s'il faut ajouter un test sur facturation_id != null
                if ($record['Activitesreelle']['VEROUILLE']==1):
                    unset($record['Activitesreelle']['created']);                
                    unset($record['Activitesreelle']['modified']);
                    $record['Activitesreelle']['created'] = $this->Activitesreelle->read('created');
                    $record['Activitesreelle']['modified'] = date('Y-m-d');                 
                    $record['Activitesreelle']['VEROUILLE'] = 0;
                    $record['Activitesreelle']['facturation_id'] = null;                
                    $this->Activitesreelle->create();
                    if ($this->Activitesreelle->save($record)) {
                        if(!$loop):
                        $this->Session->setFlash(__('Feuille de temps mise à jour pour facturation'),'default',array('class'=>'alert alert-success'));
                        endif;
                    } else {
                    if(!$loop):
                    $this->Session->setFlash(__('Feuille de temps <b>NON</b> mise à jour pour facturation'),'default',array('class'=>'alert alert-error')); 
                    endif;  
                    }
                endif;
                if(!$loop):
                $this->History->goBack();
                endif;
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();           
            endif;                
	}   
        
/**
 * errorfacturation method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function errorfacturation($id = null) {
            if (isAuthorized('activitesreelles', 'update')) :
                $this->set('title_for_layout','Feuilles de temps');  
                $this->Activitesreelle->id = $id;
                $record = $this->Activitesreelle->read();
                unset($record['Activitesreelle']['created']);                
                unset($record['Activitesreelle']['modified']);
                $record['Activitesreelle']['created'] = $this->Activitesreelle->read('created');
                $record['Activitesreelle']['modified'] = date('Y-m-d');                 
                $record['Activitesreelle']['VEROUILLE'] = 1;
                $this->Activitesreelle->create();
                if ($this->Activitesreelle->save($record)) {
                    $this->Session->setFlash(__('Feuille de temps mise à jour pour facturation'),'default',array('class'=>'alert alert-success'));
                    $this->History->goBack();
                } 
		$this->Session->setFlash(__('Feuille de temps <b>NON</b> mise à jour pour facturation'),'default',array('class'=>'alert alert-error')); 
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();           
            endif;                
	}          
        
/**
 * search method
 *
 * @return void
 */
	public function search() {
            if (isAuthorized('activitesreelles', 'index')) :
                $this->set('title_for_layout','feuilles de temps');
                $keyword=isset($this->params->data['Activitesreelle']['SEARCH']) ? $this->params->data['Activitesreelle']['SEARCH'] : ''; 
                $newconditions = array('OR'=>array("Activite.NOM LIKE '%".$keyword."%'"));
                $this->Activitesreelle->recursive = 0;
                $group = $this->Activitesreelle->find('all',array('fields'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id','Utilisateur.NOM','Utilisateur.PRENOM','COUNT(Activitesreelle.DATE) AS NBACTIVITE'),'group'=>array('Activitesreelle.DATE','Activitesreelle.utilisateur_id'),'order'=>array('Activitesreelle.utilisateur_id' => 'asc','Activitesreelle.DATE' => 'desc'),'conditions'=>$newconditions));
                $this->set('groups',$group); 
                $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG'),'conditions'=>array('Utilisateur.id > 1','Utilisateur.ACTIF'=>1,'Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id > 0'),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
                $this->set('utilisateurs',$utilisateurs);                
                $this->paginate = array_merge_recursive($this->paginate,array('conditions'=>$newconditions));
                $this->autoRender = false;
                $this->Activitesreelle->recursive = 0;
                $activitesreeelles = $this->Activitesreelle->find('all',$this->paginate);
		$this->set('activitesreelles', $activitesreeelles);             
                $this->render('index');
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();           
            endif;                
        }          
        
/**
 * ActiviteExists method
 * 
 * @param type $utilisateurId
 * @param type $date
 * @param type $activite
 * @return l'id de l'activité si elle existe
 */        
        public function ActiviteExists($utilisateurId, $date, $activite){
            $this->Activitesreelle->recursive = 0;
            $allActivite = $this->Activitesreelle->find('first',array('conditions'=>array('Activitesreelle.utilisateur_id'=>$utilisateurId,'Activitesreelle.activite_id'=>$activite,'Activitesreelle.DATE'=>$this->Activitesreelle->CUSDate($this->Activitesreelle->debutsem($date)))));
            return isset($allActivite['Activitesreelle']) ? $allActivite['Activitesreelle']['id'] : 0;
        }   
        
        public function Absences(){
            $pass = isset($this->request->data['Activitesreelle']['pass']) ? $this->request->data['Activitesreelle']['pass'] : '0';
            $this->Session->delete('history');
            $date = isset($this->request->data['Activitesreelle']['month']) ? $this->request->data['Activitesreelle']['month'] : date('Y-m-d');
            $annee = date('Y',strtotime($date));
            $mois = date('m',strtotime($date));
            $datedebut = $annee."-".$mois."-01";
            $dernierjour = date('t', mktime(0, 0, 0, $mois, 5, $annee));
            $datedebut = absstartWeek(new DateTime($datedebut));            
            $datefin = $annee."-".$mois."-".$dernierjour;
            $this->Activitesreelle->Utilisateur->recursive = -1;
            if($pass == '0'):
            $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG','Utilisateur.username','Utilisateur.NOM','Utilisateur.PRENOM','Utilisateur.DATEDEBUTACTIF','Utilisateur.FINMISSION'),'conditions'=>array('Utilisateur.id > 1','Utilisateur.ACTIF'=>1,'Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id > 0','OR' => array('AND'=>array('OR'=>array('Utilisateur.DATEDEBUTACTIF < "'.$datefin.'"','Utilisateur.DATEDEBUTACTIF IS NULL'),'Utilisateur.FINMISSION > "'.$datedebut.'"'),'Utilisateur.FINMISSION IS NULL')),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
            else:
                $monequipe = $this->requestAction('equipes/myTeam/'.userAuth('id')).userAuth('id');
                $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('fields'=>array('Utilisateur.id','Utilisateur.NOMLONG','Utilisateur.username','Utilisateur.NOM','Utilisateur.PRENOM','Utilisateur.DATEDEBUTACTIF','Utilisateur.FINMISSION'),'conditions'=>array('Utilisateur.id IN ('.$monequipe.')','Utilisateur.ACTIF'=>1,'Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.profil_id > 0','OR' => array('AND'=>array('OR'=>array('Utilisateur.DATEDEBUTACTIF < "'.$datefin.'"','Utilisateur.DATEDEBUTACTIF IS NULL'),'Utilisateur.FINMISSION > "'.$datedebut.'"'),'Utilisateur.FINMISSION IS NULL')),'order'=>array('Utilisateur.NOMLONG' => 'asc')));
            endif;
            $this->set('utilisateurs',$utilisateurs);  
            $this->Activitesreelle->recursive = 0;
            if($pass == '0'):
            $viewabsences = "SELECT 
                            Activitesreelle.DATE,
                            Activitesreelle.LU, Activitesreelle.MA, Activitesreelle.ME,  Activitesreelle.JE, Activitesreelle.VE, Activitesreelle.SA, Activitesreelle.DI,
                            Activitesreelle.LU_TYPE, Activitesreelle.MA_TYPE, Activitesreelle.ME_TYPE,  Activitesreelle.JE_TYPE, Activitesreelle.VE_TYPE, Activitesreelle.SA_TYPE, Activitesreelle.DI_TYPE,
                            Activitesreelle.utilisateur_id
                            FROM activitesreelles AS Activitesreelle 
                            LEFT JOIN activites AS Activite ON (Activitesreelle.activite_id = Activite.id) 
                            WHERE Activite.projet_id = 1 
                            AND Activitesreelle.DATE BETWEEN '".$datedebut."' AND '".$datefin."'
                            ORDER BY Activitesreelle.DATE ASC;";
            else:
            $viewabsences = "SELECT 
                            Activitesreelle.DATE,
                            Activitesreelle.LU, Activitesreelle.MA, Activitesreelle.ME,  Activitesreelle.JE, Activitesreelle.VE, Activitesreelle.SA, Activitesreelle.DI,
                            Activitesreelle.LU_TYPE, Activitesreelle.MA_TYPE, Activitesreelle.ME_TYPE,  Activitesreelle.JE_TYPE, Activitesreelle.VE_TYPE, Activitesreelle.SA_TYPE, Activitesreelle.DI_TYPE,
                            Activitesreelle.utilisateur_id
                            FROM activitesreelles AS Activitesreelle 
                            LEFT JOIN activites AS Activite ON (Activitesreelle.activite_id = Activite.id)
                            WHERE Activite.projet_id = 1 
                            AND Activitesreelle.utilisateur_id IN (".$monequipe.") AND Activitesreelle.DATE BETWEEN '".$datedebut."' AND '".$datefin."'
                            ORDER BY Activitesreelle.DATE ASC;";
            endif;
            $indisponibilites = $this->Activitesreelle->query($viewabsences);
            $this->set('indisponibilites',$indisponibilites);
        }
        
/**
 * rapport
 */        
        public function rapport() {
               $this->set('title_for_layout','Rapport des activités réelles');
            if (isAuthorized('activitesreelles', 'rapports')) :
                if ($this->request->is('post')):
                    foreach ($this->request->data['Activitesreelle']['utilisateur_id'] as &$value) {
                        @$destinatairelist .= $value.',';
                    }  
                    $destinataire = 'Activitesreelle.utilisateur_id IN ('.substr_replace($destinatairelist ,"",-1).')';
                    foreach ($this->request->data['Activitesreelle']['projet_id'] as &$value) {
                        @$projetlist .= $value.',';
                    }  
                    $domaine = 'Activite.projet_id IN ('.substr_replace($projetlist ,"",-1).')';
                    $periode = 'Activitesreelle.DATE BETWEEN "'. startWeek(CUSDate($this->request->data['Activitesreelle']['START'])).'" AND "'.  endWeek(CUSDate($this->request->data['Activitesreelle']['END'])).'"';
                    $rapportresult = $this->Activitesreelle->find('all',array('fields'=>array('MONTH(Activitesreelle.DATE) AS MONTH', 'YEAR(Activitesreelle.DATE) AS YEAR','Activite.projet_id','SUM(Activitesreelle.TOTAL) AS NB'),'conditions'=>array($destinataire,$domaine,$periode),'order'=>array('MONTH(Activitesreelle.DATE)'=>'asc','YEAR(Activitesreelle.DATE)'=>'asc'),'group'=>array('Activite.projet_id','MONTH(Activitesreelle.DATE)','YEAR(Activitesreelle.DATE)'),'recursive'=>0));
                    $this->set('rapportresults',$rapportresult); //'Activite.projet_id>1',
                    $activitefirstweek = $this->Activitesreelle->find('all',array('fields'=>array('Activite.id','Activite.projet_id','Activitesreelle.domaine_id', 'SUM(Activitesreelle.LU) AS LU', 'SUM(Activitesreelle.MA) AS MA', 'SUM(Activitesreelle.ME) AS ME', 'SUM(Activitesreelle.JE) AS JE', 'SUM(Activitesreelle.VE) AS VE', 'SUM(Activitesreelle.SA) AS SA', 'SUM(Activitesreelle.DI) AS DI','Activitesreelle.DATE AS DATE'),'conditions'=>array($destinataire,$domaine,'Activitesreelle.DATE'=>startWeek(CUSDate($this->request->data['Activitesreelle']['START']))),'group'=>array('Activite.projet_id','Activite.id','Activitesreelle.domaine_id'),'recursive'=>0));  
                    $activitelastweek = $this->Activitesreelle->find('all',array('fields'=>array('Activite.id','Activite.projet_id','Activitesreelle.domaine_id', 'SUM(Activitesreelle.LU) AS LU', 'SUM(Activitesreelle.MA) AS MA', 'SUM(Activitesreelle.ME) AS ME', 'SUM(Activitesreelle.JE) AS JE', 'SUM(Activitesreelle.VE) AS VE', 'SUM(Activitesreelle.SA) AS SA', 'SUM(Activitesreelle.DI) AS DI','Activitesreelle.DATE AS DATE'),'conditions'=>array($destinataire,$domaine,'Activitesreelle.DATE'=>startWeek(CUSDate($this->request->data['Activitesreelle']['END']))),'group'=>array('Activite.projet_id','Activite.id','Activitesreelle.domaine_id'),'recursive'=>0));  
                    $entrop = array_merge($this->getEntropFirst($activitefirstweek),$this->getEntropLast($activitelastweek));
                    $byprojet = $this->array_sum_merge_by_projet($entrop);
                    $byprojetdomaine = $this->array_sum_merge_by_projet_domaine($entrop);
                    $byprojetactivite = $this->array_sum_merge_by_projet_activite($entrop);
                    $this->set('byprojet',$byprojet); 
                    $this->set('byprojetdomaine',$byprojetdomaine);
                    $this->set('byprojetactivite',$byprojetactivite);
                    $chartresult = $this->Activitesreelle->find('all',array('fields'=>array('Activite.projet_id','SUM(Activitesreelle.TOTAL) AS NB'),'conditions'=>array($destinataire,$domaine,$periode),'order'=>array('Activite.projet_id'=>'asc'),'group'=>array('Activite.projet_id'),'recursive'=>0));
                    $this->set('chartresults',$chartresult);                    
                    $detailrapportresult = $this->Activitesreelle->find('all',array('fields'=>array('MONTH(Activitesreelle.DATE) AS MONTH', 'YEAR(Activitesreelle.DATE) AS YEAR','Activite.NOM','Activite.id','Activite.projet_id','SUM(Activitesreelle.TOTAL) AS NB'),'conditions'=>array($destinataire,$domaine,$periode),'order'=>array('MONTH(Activitesreelle.DATE)'=>'asc','YEAR(Activitesreelle.DATE)'=>'asc'),'group'=>array('Activite.projet_id','Activite.NOM','MONTH(Activitesreelle.DATE)','YEAR(Activitesreelle.DATE)'),'recursive'=>0));
                    $this->set('detailrapportresults',$detailrapportresult);
                    $rapportdomainesresult = $this->Activitesreelle->find('all',array('fields'=>array('MONTH(Activitesreelle.DATE) AS MONTH', 'YEAR(Activitesreelle.DATE) AS YEAR','Activite.projet_id','Activitesreelle.domaine_id','SUM(Activitesreelle.TOTAL) AS NB','Domaine.NOM'),'conditions'=>array($destinataire,$domaine,$periode),'order'=>array('MONTH(Activitesreelle.DATE)'=>'asc','YEAR(Activitesreelle.DATE)'=>'asc'),'group'=>array('Activite.projet_id','Activitesreelle.domaine_id','MONTH(Activitesreelle.DATE)','YEAR(Activitesreelle.DATE)'),'recursive'=>0));
                    $this->set('rapportdomainesresults',$rapportdomainesresult);                    
                    $this->Session->delete('rapportresults');  
                    $this->Session->delete('detailrapportresults');     
                    $this->Session->delete('rapportdomainesresults');                      
                    $this->Session->write('rapportdomainesresults',$rapportdomainesresult);                    
                    $this->Session->write('rapportresults',$rapportresult);
                    $this->Session->write('detailrapportresults',$detailrapportresult);
                endif;
                $alldestinataire = array('tous'=>'Tous les responsables');
                $listeagentsavecsaisie = $this->Activitesreelle->find('all',array('fields'=>array('Activitesreelle.utilisateur_id'),'group'=>'Activitesreelle.utilisateur_id','order'=>array('Activitesreelle.utilisateur_id'=>'asc'),'recursive'=>0));
                $listein = '';
                foreach($listeagentsavecsaisie as $agent):
                    $listein .= $agent['Activitesreelle']['utilisateur_id'].',';
                endforeach;
                $destinataires = $this->Activitesreelle->Utilisateur->find('list',array('fields'=>array('id','NOMLONG'),'conditions'=>array('Utilisateur.id in ('.substr_replace($listein ,"",-1).')'),'order'=>array('Utilisateur.NOMLONG'=>'asc'),'recursive'=>-1));
                $this->set('destinataires',$destinataires);  
                $domaines = $this->Activitesreelle->Activite->Projet->find('list',array('fields'=>array('id','NOM'),'order'=>array('Projet.NOM'),'recursive'=>-1));
                $this->set('domaines',$domaines);                
            else :
                $this->Session->setFlash(__('Action non autorisée, veuillez contacter l\'administrateur.'),'default',array('class'=>'alert alert-block'));
                throw new NotAuthorizedException();
            endif;                
	} 
        
        public function getEntropFirst($activitefirstweek=null){
            $enmoins1 = array();
            $i=0;
            foreach($activitefirstweek as $firstweek):
                $datetime1 = new DateTime(CUSDate($firstweek['Activitesreelle']['DATE']));
                $datetime2 = new DateTime(CUSDate($this->request->data['Activitesreelle']['START']));
                $interval = $datetime2->diff($datetime1); 
                $entropfirst = $interval->format('%a');
                switch ($entropfirst):
                    case 1:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');                        
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU'];  
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                       
                        break;
                    case 2:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');                        
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU']+$firstweek[0]['MA'];
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                        
                        break;
                    case 3:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');                        
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];                        
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU']+$firstweek[0]['MA']+$firstweek[0]['ME'];
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                        
                        break;
                    case 4:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');                        
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];                        
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];                       
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU']+$firstweek[0]['MA']+$firstweek[0]['ME']+$firstweek[0]['JE'];
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                         
                        break;
                    case 5:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');                        
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];                        
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];                        
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU']+$firstweek[0]['MA']+$firstweek[0]['ME']+$firstweek[0]['JE']+$firstweek[0]['VE'];
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                         
                        break;
                    case 6:
                        $enmoins1[$i]['date']=$datetime1;
                        $enmoins1[$i]['mois']=$datetime1->format('m');
                        $enmoins1[$i]['projet_id']=$firstweek['Activite']['projet_id'];
                        $enmoins1[$i]['activite_id']=$firstweek['Activite']['id'];                        
                        $enmoins1[$i]['domaine_id']=$firstweek['Activitesreelle']['domaine_id'];                        
                        $enmoins1[$i]['aretirer']=$firstweek[0]['LU']+$firstweek[0]['MA']+$firstweek[0]['ME']+$firstweek[0]['JE']+$firstweek[0]['VE']+$firstweek[0]['SA'];
                        $enmoins1[$i]['key']=$firstweek['Activite']['projet_id'].$firstweek['Activite']['id'].$firstweek['Activitesreelle']['domaine_id'];                       
                        break;
                endswitch;
                $i++;
            endforeach;                           
            return $enmoins1;
        }
        
        public function getEntropLast($activitelastweek=null){
            $enmoins2 = array();
            $i=0;            
            foreach($activitelastweek as $lastweek):
                $datetime1 = new DateTime(CUSDate($lastweek['Activitesreelle']['DATE']));
                $datetime2 = new DateTime(CUSDate($this->request->data['Activitesreelle']['END']));
                $interval = $datetime1->diff($datetime2); 
                $entropfirst = $interval->format('%a');
                switch ($entropfirst):
                    case 1:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                        
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI']+$lastweek[0]['SA']+$lastweek[0]['VE']+$lastweek[0]['JE']+$lastweek[0]['ME'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                      
                        break;
                    case 2:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                           
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI']+$lastweek[0]['SA']+$lastweek[0]['VE']+$lastweek[0]['JE'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                         
                        break;
                    case 3:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                           
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI']+$lastweek[0]['SA']+$lastweek[0]['VE'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                          
                        break;
                    case 0:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                           
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI']+$lastweek[0]['SA']+$lastweek[0]['VE']+$lastweek[0]['JE']+$lastweek[0]['ME']+$lastweek[0]['MA'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                         
                        break;
                    case 5:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                           
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                         
                        break;
                    case 4:
                        $enmoins2[$i]['date']=$datetime1;
                        $enmoins2[$i]['mois']=$datetime1->format('m');                        
                        $enmoins2[$i]['projet_id']=$lastweek['Activite']['projet_id'];
                        $enmoins2[$i]['activite_id']=$lastweek['Activite']['id'];                        
                        $enmoins2[$i]['domaine_id']=$lastweek['Activitesreelle']['domaine_id'];                           
                        $enmoins2[$i]['aretirer']=$lastweek[0]['DI']+$lastweek[0]['SA'];
                        $enmoins2[$i]['key']=$lastweek['Activite']['projet_id'].$lastweek['Activite']['id'].$lastweek['Activitesreelle']['domaine_id'];                        
                        break;
                endswitch;
                $i++;
            endforeach;                         
            return $enmoins2;
        }

        public function array_sum_merge($array){
            $sortedArray = array();
            $assignedValues = array();
            foreach ($array as $arrayItem)
            {
                $ukey = $arrayItem['projet_id'].$arrayItem['activite_id'].$arrayItem['domaine_id'];
                if (!isset($sortedArray[$ukey])){
                    $sortedArray[$ukey] = array();
                }
                $sortedArray[$ukey][] = $arrayItem['aretirer'];
                if (!isset($assignedValues[$ukey])){
                    $assignedValues[$ukey] = array(
                        'projet_id' => $arrayItem['projet_id'],
                        'activite_id' => $arrayItem['activite_id'],
                        'domaine_id' => $arrayItem['domaine_id'],
                        'mois' => $arrayItem['mois'],
                        'date' => $arrayItem['date']
                    );
                }
            }
            $result = array();
            $i=0;
            foreach ($sortedArray as $ukey => $arrayItem)
            {
               $sum = array_sum($arrayItem);

               $result[$i] = $assignedValues[$ukey];
               $result[$i]['sum'] =$sum;
               $i++;
            }    
            return $result;
        }
        
        public function array_sum_merge_by_projet($array){
            $sortedArray = array();
            $assignedValues = array();
            foreach ($array as $arrayItem)
            {
                $ukey = $arrayItem['projet_id'];
                if (!isset($sortedArray[$ukey])){
                    $sortedArray[$ukey] = array();
                }
                $sortedArray[$ukey][] = $arrayItem['aretirer'];
                if (!isset($assignedValues[$ukey])){
                    $assignedValues[$ukey] = array(
                        'projet_id' => $arrayItem['projet_id'],
                        'mois' => $arrayItem['mois'],
                        'date' => $arrayItem['date']
                    );
                }
            }
            $result = array();
            $i=0;
            foreach ($sortedArray as $ukey => $arrayItem)
            {
               $sum = array_sum($arrayItem);

               $result[$i] = $assignedValues[$ukey];
               $result[$i]['sum'] =$sum;
               $i++;
            }    
            return $result;
        }        

        public function array_sum_merge_by_projet_domaine($array){
            $sortedArray = array();
            $assignedValues = array();
            foreach ($array as $arrayItem)
            {
                $ukey = $arrayItem['projet_id'].$arrayItem['domaine_id'];
                if (!isset($sortedArray[$ukey])){
                    $sortedArray[$ukey] = array();
                }
                $sortedArray[$ukey][] = $arrayItem['aretirer'];
                if (!isset($assignedValues[$ukey])){
                    $assignedValues[$ukey] = array(
                        'projet_id' => $arrayItem['projet_id'],
                        'domaine_id' => $arrayItem['domaine_id'],
                        'mois' => $arrayItem['mois'],
                        'date' => $arrayItem['date']
                    );
                }
            }
            $result = array();
            $i=0;
            foreach ($sortedArray as $ukey => $arrayItem)
            {
               $sum = array_sum($arrayItem);

               $result[$i] = $assignedValues[$ukey];
               $result[$i]['sum'] =$sum;
               $i++;
            }    
            return $result;
        } 
        
        public function array_sum_merge_by_projet_activite($array){
            $sortedArray = array();
            $assignedValues = array();
            foreach ($array as $arrayItem)
            {
                $ukey = $arrayItem['projet_id'].$arrayItem['activite_id'];
                if (!isset($sortedArray[$ukey])){
                    $sortedArray[$ukey] = array();
                }
                $sortedArray[$ukey][] = $arrayItem['aretirer'];
                if (!isset($assignedValues[$ukey])){
                    $assignedValues[$ukey] = array(
                        'projet_id' => $arrayItem['projet_id'],
                        'activite_id' => $arrayItem['activite_id'],
                        'mois' => $arrayItem['mois'],
                        'date' => $arrayItem['date']
                    );
                }
            }
            $result = array();
            $i=0;
            foreach ($sortedArray as $ukey => $arrayItem)
            {
               $sum = array_sum($arrayItem);

               $result[$i] = $assignedValues[$ukey];
               $result[$i]['sum'] =$sum;
               $i++;
            }    
            return $result;
        } 
        
        function export_doc() {
            if($this->Session->check('rapportresults') && $this->Session->check('detailrapportresults')):
                $data = $this->Session->read('rapportresults');
                $this->set('rowsrapport',$data);
                $data = $this->Session->read('detailrapportresults'); 
                $this->set('rowsdetail',$data);              
		$this->render('export_doc','export_doc');
            else:
                $this->Session->setFlash(__('Rapport impossible à éditer veuillez renouveler le calcul du rapport'),'default',array('class'=>'alert alert-error'));             
                $this->redirect(array('action'=>'rapport'));
            endif;
        }         
        
        public function facturer(){
            $ids = explode('-', $this->request->data('all_ids'));
            if(count($ids)>0 && $ids[0]!=""):
                foreach($ids as $id):
                    $newFacturation = array();
                    $record = $this->Activitesreelle->find('first',array('conditions'=>array('Activitesreelle.id'=>$id)));                  
                    $newFacturation['Facturation']['activitesreelle_id']=$id;
                    $newFacturation['Facturation']['utilisateur_id']=$record['Activitesreelle']['utilisateur_id'];
                    $newFacturation['Facturation']['activite_id']=$record['Activitesreelle']['activite_id'];
                    $newFacturation['Facturation']['VERSION']=0;                    
                    $newFacturation['Facturation']['NUMEROFTGALILEI']="0000000000";
                    $newFacturation['Facturation']['DATE']=$record['Activitesreelle']['DATE'];
                    $newFacturation['Facturation']['LU']=$record['Activitesreelle']['LU'];
                    $newFacturation['Facturation']['MA']=$record['Activitesreelle']['MA'];
                    $newFacturation['Facturation']['ME']=$record['Activitesreelle']['ME'];
                    $newFacturation['Facturation']['JE']=$record['Activitesreelle']['JE'];
                    $newFacturation['Facturation']['VE']=$record['Activitesreelle']['VE'];
                    $newFacturation['Facturation']['SA']=$record['Activitesreelle']['SA'];
                    $newFacturation['Facturation']['DI']=$record['Activitesreelle']['DI'];
                    $newFacturation['Facturation']['TOTAL']=$record['Activitesreelle']['TOTAL'];
                    $this->Activitesreelle->Facturation->create();
                    if($this->Activitesreelle->Facturation->save($newFacturation)){
                        $lastInsert = $this->Activitesreelle->Facturation->getLastInsertID();
                        $this->Activitesreelle->id = $id;
                        $this->Activitesreelle->saveField('facturation_id', $lastInsert);
                    }
                endforeach;
                echo $this->Session->setFlash(__('Feuilles de temps facturées'),'default',array('class'=>'alert alert-success'));
            else:
                echo $this->Session->setFlash(__('Aucune feuilles de temps sélectionnées'),'default',array('class'=>'alert alert-error'));
            endif;
            exit();
        }
        
        public function rejeter(){
            $ids = explode ('-', $this->request->data('all_ids'));
            if(count($ids)>0 && $ids[0]!=""):            
                foreach($ids as $id):
                    $this->Activitesreelle->id = $id;
                    $this->Activitesreelle->saveField('VEROUILLE', 1);                    
                endforeach;
                echo $this->Session->setFlash(__('Feuilles de temps rejetées'),'default',array('class'=>'alert alert-success'));
            else:
                echo $this->Session->setFlash(__('Aucune feuilles de temps sélectionnées'),'default',array('class'=>'alert alert-error'));                
            endif;
            exit();
        }  
        
        public function deverouiller($id){
            $this->Activitesreelle->id = $id;
            $this->Activitesreelle->saveField('VEROUILLE', 1);        
            $this->Activitesreelle->saveField('facturation_id', null);   
            $facturation = $this->Activitesreelle->Facturation->find('first',array('conditions'=>array('Facturation.activitesreelle_id'=>$id,'Facturation.VISIBLE'=>0),'recursive'=>-1));
            $this->Activitesreelle->Facturation->id = $facturation['Facturation']['id'];
            $this->Activitesreelle->Facturation->saveField('VISIBLE', 1);
            echo $this->Session->setFlash(__('Feuille de temps déverouillée'),'default',array('class'=>'alert alert-success'));
            $this->History->goBack();
        } 
          
/**
 * export_xls
 * 
 */       
	function export_xls() {
		$data = $this->Session->read('xls_export');
                $this->Session->delete('xls_export');                
		$this->set('rows',$data);
		$this->render('export_xls','export_xls');
        }    
        
        public function homeNBActivitesReelles(){
            $lastMonthDay = date('Y-m-').date('t');
            $nbactions = $this->Activitesreelle->find('all',array('fields'=>array('SUM(TOTAL) AS TOTAL','DATE','VEROUILLE'),'conditions'=>array('utilisateur_id'=>userAuth('id'),"DATE BETWEEN '".date('Y-m-01')."' AND '".$lastMonthDay."'"),'group'=>'DATE','recursive'=>-1));
            return $nbactions;
        }    
        
        public function isUnique($utilisateur_id,$activite_id,$domaine_id,$date){
            $result = $this->Activitesreelle->find('count',array('conditions'=>array('Activitesreelle.utilisateur_id'=>$utilisateur_id,'Activitesreelle.activite_id'=>$activite_id,'Activitesreelle.domaine_id'=>$domaine_id,'Activitesreelle.DATE'=>$date)));
            return $result > 0 ? false : true;
        }

        public function isExist($utilisateur_id,$activite_id,$date){
            $result = $this->Activitesreelle->find('all',array('conditions'=>array('Activitesreelle.utilisateur_id'=>$utilisateur_id,'Activitesreelle.activite_id'=>$activite_id,'Activitesreelle.DATE'=>$date),'recursive'=>-1));
            return count($result)>0 ? $result : false;
        }
        
        public function soumettre(){
            $ids = explode('-', $this->request->data('all_ids'));
            if(count($ids)>0 && $ids[0]!=""):
                foreach($ids as $id):
                    $this->updatefacturation($id,true);
                endforeach;    
                $this->History->goBack();
            endif;
            exit();
        }
        
        public function deleteall(){
            $ids = explode('-', $this->request->data('all_ids'));
            if(count($ids)>0 && $ids[0]!=""):
                foreach($ids as $id):
                    $this->delete($id,true);
                endforeach;  
                $this->History->goBack();
            endif;
            exit();
        }     
        
        public function icsImport($utilisateur_id,$activite_id,$date,$day,$type=null,$duree=null){
            $datetime = new DateTime($date);
            $type = $type==null ? 1 : isFerie($datetime) ? 1 :$type;
            $duree = $duree==null ? 0 : isFerie($datetime) ? 0 : $duree;
            //$type = $type==null ? 1 : $type;
            //$duree = $duree==null ? 0 : $duree;            
            $activitesreelle = $this->isExist($utilisateur_id, $activite_id, $date);
            $record['Activitesreelle']['TOTAL']=0;
            $record['Activitesreelle']['utilisateur_id'] = $utilisateur_id;
            $record['Activitesreelle']['activite_id'] = $activite_id;
            $record['Activitesreelle']['DATE'] = CFRDate($date);
            $record['Activitesreelle']['created'] = date('Y-m-d'); 
            $record['Activitesreelle']['modified'] = date('Y-m-d'); 
            if ($activitesreelle!=false):
                $this->Activitesreelle->id=$activitesreelle[0]['Activitesreelle']['id'];
                $record['Activitesreelle']['TOTAL']=$activitesreelle[0]['Activitesreelle']['TOTAL'];
                $record['Activitesreelle']['created'] = $activitesreelle[0]['Activitesreelle']['created'];
            else:
                $this->Activitesreelle->create();
            endif;
            switch($day):
                case 'LU':
                    $record['Activitesreelle']['LU']=$duree;
                    $record['Activitesreelle']['LU_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                case 'MA':
                    $record['Activitesreelle']['MA']=$duree;
                    $record['Activitesreelle']['MA_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                    $record['Activitesreelle']['ME']=$duree;
                    $record['Activitesreelle']['ME_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                case 'JE':
                    $record['Activitesreelle']['JE']=$duree;
                    $record['Activitesreelle']['JE_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                case 'VE':
                    $record['Activitesreelle']['VE']=$duree;
                    $record['Activitesreelle']['VE_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                //JLR :: pas necessaire d'importer les samedi et dimanche
                /*case 'SA':
                    $record['Activitesreelle']['SA']=$duree;
                    $record['Activitesreelle']['SA_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;
                case 'DI':
                    $record['Activitesreelle']['DI']=$duree;
                    $record['Activitesreelle']['DI_TYPE']=$type;
                    $this->Activitesreelle->save($record);
                    break;   */             
            endswitch;
            //JLR :: retirer les samedi et dimanche du total
            $total = $this->Activitesreelle->find('first',array('fields'=>array('(LU+MA+ME+JE+VE) AS TOTAL','DATE','VEROUILLE'),'conditions'=>array('Activitesreelle.utilisateur_id'=>$utilisateur_id,'Activitesreelle.activite_id'=>$activite_id,'Activitesreelle.DATE'=>$date),'recursive'=>-1));
            $record['Activitesreelle']['TOTAL'] = $total[0]['TOTAL'];
            $this->Activitesreelle->save($record);
        }
        
        public function sendmailabsences($activitesreelle){
            $activite = $this->Activitesreelle->Activite->find('first',array('conditions'=>array('Activite.id'=>$activitesreelle['activite_id'])));
            $valideurs = $this->Activitesreelle->Utilisateur->Equipe->find('all',array('conditions'=>array('Equipe.agent'=>userAuth('id'))));
            $mailto = array();
            foreach($valideurs as $valideur):
                $mailto[]=$valideur['Utilisateur']['MAIL'];
            endforeach;
            $to=$mailto;
            $from = userAuth('MAIL');
            $objet = 'SAILL : Demande d\'absences ['.$activite['Activite']['NOM'].']';
            $message = "Demande d'absences pour la semaine débutant le ".CFRDate($activitesreelle['DATE']).
                    '<ul>
                    <li>LU :'.$activitesreelle['LU'].'</li>
                    <li>MA :'.$activitesreelle['MA'].'</li>
                    <li>ME :'.$activitesreelle['ME'].'</li>
                    <li>JE :'.$activitesreelle['JE'].'</li>
                    <li>VE :'.$activitesreelle['VE'].'</li>
                    <li>SA :'.$activitesreelle['SA'].'</li>
                    <li>DI :'.$activitesreelle['DI'].'</li>                        
                    </ul>';
            if($to!=''):
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
                    $this->Session->setFlash(__('Erreur lors de l\'envois du mail - '.translateMailException($e->getMessage())),'default',array('class'=>'alert alert-error'));
                }  
            endif;
        }
        
        public function getActivitesReelles($mois,$annee){
            $lastMonthDay = $annee.'-'.$mois.'-'.date('t');
            $firstMonthDay = startWeek($annee.'-'.$mois.'-01');
            $sql = "CREATE VIEW SAISIE AS
                    SELECT SUM(activitesreelles.TOTAL) AS TOTAL,CONCAT(utilisateurs.NOM,' ',utilisateurs.PRENOM) AS NOMLONG,utilisateurs.id AS USERID, SUM(activitesreelles.VEROUILLE) AS VEROUILLE
                    FROM activitesreelles
                    LEFT JOIN utilisateurs ON activitesreelles.utilisateur_id = utilisateurs.id
                    WHERE activitesreelles.DATE BETWEEN '".$firstMonthDay."' AND '".$lastMonthDay."'
                        AND utilisateurs.profil_id > 0
                    GROUP BY activitesreelles.utilisateur_id
                    ORDER BY CONCAT(utilisateurs.NOM,' ',utilisateurs.PRENOM) ASC";
            $select = "SELECT * FROM SAISIE"; 
            $this->Activitesreelle->query("DROP VIEW IF EXISTS SAISIE;");
            $this->Activitesreelle->query($sql);
            $nbsaisie = $this->Activitesreelle->query($select);
            $this->Activitesreelle->query("DROP VIEW IF EXISTS SAISIE;");
            return $nbsaisie;
        }       
        
        public function saisieVide($mois,$annee){
            $utilisateurs = $this->Activitesreelle->Utilisateur->find('all',array('conditions'=>array('Utilisateur.GESTIONABSENCES'=>1,'Utilisateur.ACTIF'=>1),'recursive'=>-1));
            $allIdUsers = array();
            foreach($utilisateurs as $utilisateur):
                $allIdUsers[] = $utilisateur['Utilisateur']['id'];
            endforeach;
            $utilisateurAvecSaisie = $this->getActivitesReelles($mois, $annee);
            $userWithWork = array();
            foreach($utilisateurAvecSaisie as $utilisateur):
                $userWithWork[] = $utilisateur['SAISIE']['USERID'];
            endforeach;            
            $result = implode(",",array_diff($allIdUsers, $userWithWork));
            return $this->Activitesreelle->Utilisateur->find('all',array('conditions'=>array('Utilisateur.id in ('.$result.')'),'order'=>array('Utilisateur.NOMLONG'=>'asc'),'recursive'=>-1));
        }
        
        public function sendmailrelance($utilisateur_id){
            $utilisateur = $this->Activitesreelle->Utilisateur->find('first', array('conditions'=>array('Utilisateur.id'=>$utilisateur_id),'recursive'=>0));  
            $from = userAuth('MAIL');
            $to=$utilisateur['Utilisateur']['MAIL'];
            $email = $utilisateur['Utilisateur']['MAIL'];
            $objet = 'SAILL : //!\ URGENT RELANCE : Saisie d\'activité';
            $message = "URGENT RELANCE : Bonjour ".$utilisateur['Utilisateur']['NOMLONG'].',';
            $message .= $message.'<br>
                    Votre saisie ne semble pas avoir été faites dans l\'outil.<br>
                    Merci de prendre quelques minutes de votre temps pour faire cette saisie sur le mois.<br><br>
                    N\'oubliez pas que vous pouvez saisir par anticipation votre activité et la valider juste avant la date limite.<br>
                    Merci de votre compréhension.';
            if($to!=''):
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
                    $this->Session->setFlash(__('Erreur lors de l\'envois du mail - '.translateMailException($e->getMessage())),'default',array('class'=>'alert alert-error'));
                }  
            endif;
        }         
}
