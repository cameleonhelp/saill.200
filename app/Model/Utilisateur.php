<?php
App::uses('AppModel', 'Model','AuthComponent', 'Controller/Component');
/**
 * Utilisateur Model
 *
 * @property Profil $Profil
 * @property Societe $Societe
 * @property Assistance $Assistance
 * @property Section $Section
 * @property Utilisateur $Utilisateur
 * @property Domaine $Domaine
 * @property Site $Site
 * @property Tjmagent $Tjmagent
 * @property Dotation $Dotation
 * @property Action $Action
 * @property Affectation $Affectation
 * @property Dotation $Dotation
 * @property Historyaction $Historyaction
 * @property Historyutilisateur $Historyutilisateur
 * @property Linkshared $Linkshared
 * @property Listediffusion $Listediffusion
 * @property Livrable $Livrable
 * @property Outil $Outil
 * @property Section $Section
 * @property Utilisateur $Utilisateur
 * @property Utiliseoutil $Utiliseoutil
 */
class Utilisateur extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'profil_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'ACTIF' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'NOM' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'PRENOM' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'WORKCAPACITY' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'CONGE' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'RQ' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'VT' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'HIERARCHIQUE' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'GESTIONABSENCES' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Profil' => array(
			'className' => 'Profil',
			'foreignKey' => 'profil_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Societe' => array(
			'className' => 'Societe',
			'foreignKey' => 'societe_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Assistance' => array(
			'className' => 'Assistance',
			'foreignKey' => 'assistance_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'section_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Hierarchique' => array(
			'className' => 'Utilisateur',
			'foreignKey' => 'utilisateur_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Domaine' => array(
			'className' => 'Domaine',
			'foreignKey' => 'domaine_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Tjmagent' => array(
			'className' => 'Tjmagent',
			'foreignKey' => 'tjmagent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Dotation' => array(
			'className' => 'Dotation',
			'foreignKey' => 'dotation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Assoentiteutilisateur' => array(
			'className' => 'Assoentiteutilisateur',
			'foreignKey' => 'utilisateur_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
                    
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Action' => array(
			'className' => 'Action',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
                'Action' => array(
			'className' => 'Action',
			'foreignKey' => 'destinataire',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),            
		'Affectation' => array(
			'className' => 'Affectation',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Dotation' => array(
			'className' => 'Dotation',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Dossierpartage' => array(
			'className' => 'Dossierpartage',
			'foreignKey' => '',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Activite' => array(
			'className' => 'Activite',
			'foreignKey' => '',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
                'Historyutilisateur' => array(
			'className' => 'Historyutilisateur',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Linkshared' => array(
			'className' => 'Linkshared',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Listediffusion' => array(
			'className' => 'Listediffusion',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Livrable' => array(
			'className' => 'Livrable',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Outil' => array(
			'className' => 'Outil',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Utilisateur' => array(
			'className' => 'Utilisateur',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Utiliseoutil' => array(
			'className' => 'Utiliseoutil',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Equipe' => array(
			'className' => 'Equipe',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
                'Assoentiteutilisateur' => array(
			'className' => 'Assoentiteutilisateur',
			'foreignKey' => 'utilisateur_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)             
	);

        public $virtualFields = array(
            'NOMLONG' => 'CONCAT(Utilisateur.NOM, " ", Utilisateur.PRENOM)'
        );
 
 /**
 * beforeSave method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param none
 * @return void
 */
        public function beforeSave($options = array()) {
            if (!empty($this->data['Utilisateur']['password'])) {
                $this->data['Utilisateur']['password'] = Security::hash($this->data['Utilisateur']['password'],'md5',false); 
            }
            if (!empty($this->data['Utilisateur']['NAISSANCE'])) {
                $this->data['Utilisateur']['NAISSANCE'] = $this->dateFormatBeforeSave($this->data['Utilisateur']['NAISSANCE']);
            }
            if (!empty($this->data['Utilisateur']['FINMISSION'])) {
                $this->data['Utilisateur']['FINMISSION'] = $this->dateFormatBeforeSave($this->data['Utilisateur']['FINMISSION']);
            }
            if (!empty($this->data['Utilisateur']['DATEDEBUTACTIF'])) {
                $this->data['Utilisateur']['DATEDEBUTACTIF'] = $this->dateFormatBeforeSave($this->data['Utilisateur']['DATEDEBUTACTIF']);
            }
            if (!empty($this->data['Utilisateur']['NOM'])) {
                $this->data['Utilisateur']['NOM'] = strtoupper($this->data['Utilisateur']['NOM']);
            }    
            if (!empty($this->data['Utilisateur']['PRENOM'])) {
                $this->data['Utilisateur']['PRENOM'] = ucfirst_utf8($this->data['Utilisateur']['PRENOM']);
            }                 
            parent::beforeSave();
            return true;
        }
        
/**
 * afterFind method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param none
 * @return void
 */
        public function afterFind($results, $primary = false) {
            foreach ($results as $key => $val) {
                if (isset($val['Utilisateur']['created'])) {
                    $results[$key]['Utilisateur']['created'] = $this->dateFormatAfterFind($val['Utilisateur']['created']);
                }      
                if (isset($val['Utilisateur']['modified'])) {
                    $results[$key]['Utilisateur']['modified'] = $this->dateFormatAfterFind($val['Utilisateur']['modified']);
                }            
                if (isset($val['Utilisateur']['NAISSANCE'])) {
                    $results[$key]['Utilisateur']['NAISSANCE'] = $this->dateFormatAfterFind($val['Utilisateur']['NAISSANCE']);
                } 
                if (isset($val['Utilisateur']['FINMISSION'])) {
                    $results[$key]['Utilisateur']['FINMISSION'] = $this->dateFormatAfterFind($val['Utilisateur']['FINMISSION']);
                } 
                if (isset($val['Utilisateur']['DATEDEBUTACTIF'])) {
                    $results[$key]['Utilisateur']['DATEDEBUTACTIF'] = $this->dateFormatAfterFind($val['Utilisateur']['DATEDEBUTACTIF']);
                } 
                if (isset($val['Utilisateur']['DATEENGCONF'])) {
                    $results[$key]['Utilisateur']['DATEENGCONF'] = $this->dateFormatAfterFind($val['Utilisateur']['DATEENGCONF']);
                }                 
                if (isset($val['Utilisateur']['societe_id'])) {
                    $results[$key]['Utilisateur']['societe_NOM'] = $this->getSocieteForUser($val['Utilisateur']['societe_id']);
                } 
                /*
                 * TODO faire ces mise à jour par batch une fois dans la journée
                if (isset($val['Utilisateur']['FINMISSION']) && ($val['Utilisateur']['FINMISSION']!=null || $val['Utilisateur']['FINMISSION']!= '0000-00-00') && $val['Utilisateur']['FINMISSION'] < date('d/m/Y') && isset($val['Utilisateur']['ACTIF'])) {
                    if(isset($val['Utilisateur']['id']) && isset($val['Utilisateur']['FINMISSION'])):
                        //$this->autoend($val['Utilisateur']['id'],$val['Utilisateur']['FINMISSION'],$val['Utilisateur']['ACTIF']);
                    endif;
                 } 
                if (isset($val['Utilisateur']['ACTIF']) && ($val['Utilisateur']['ACTIF']!=null || $val['Utilisateur']['ACTIF']== 0)) {
                    if(isset($val['Utilisateur']['id']) && isset($val['Utilisateur']['FINMISSION'])):
                        //$this->purge($val['Utilisateur']['id'],$val['Utilisateur']['FINMISSION'],$val['Utilisateur']['ACTIF']);
                    endif;
                }    
                 * 
                 */             
            }
            return $results;
        }   
        
        public function getSocieteForUser($id){
            $sql = "SELECT NOM FROM societes WHERE id='".$id."'";
            $result = $this->query($sql);
            return  !empty($result) ? $result[0]['societes']['NOM'] : "";
        }   
        
        public function autoend($id,$date,$actif){
            $today = new DateTime();
            $today->format('Y-m-d');
            $date = new DateTime($date);
            $diff = $today->diff($date);
            if($diff->invert ==1 && $actif==1):
                $sql = "UPDATE utilisateurs SET ACTIF=0,FINMISSION='".$today->format('Y-m-d')."',modified='".$today->format('Y-m-d')."' WHERE id='".$id."'";
                $result = $this->query($sql);
            endif;
        }   
        
        public function purge($id,$date,$actif){
            if(isset($id) && isset($date)):
                $today = new DateTime();
                $today->format('Y-m-d');
                $date = new DateTime($date);
                $diff = $today->format('m') - $date->format('m');
                if($diff>=1 && $actif==0):
                    $sql = "UPDATE utilisateurs SET GESTIONABSENCES=0,modified='".$today->format('Y-m-d')."' WHERE id='".$id."'";
                    $result = $this->query($sql);
                endif; 
            endif;
        }
}
