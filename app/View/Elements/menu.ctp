<div class="dropdown clearfix">
  <?php  $controller = $this->params['controller'] != 'pages' ? strtolower($this->params['controller'].'_'.$this->params['action']) : strtolower($this->params['controller'].'_'.$this->params['pass'][0]);?>  
  <!-- classes pour rendre le menu actif si en se situe sur le controller //-->  
  <?php $classProfil = in_array($controller,array('utilisateurs_profil')) ? 'active' : ''; ?>
  <?php $classLogout = in_array($controller,array('utilisateurs_logout')) ? 'active' : ''; ?>
  <?php $classHome = in_array($controller,array('pages_home')) ? 'active' : ''; ?>
  <?php $classAction = in_array($controller,array('actions_index','actions_add','actions_edit','actions_delete','actions_search')) ? 'active' : ''; ?>
  <?php $classActvitereelle = in_array($controller,array('activitesreelles_index','activitesreelles_add','activitesreelles_edit','activitesreelles_delete','activitesreelles_search')) ? 'active' : ''; ?>  
  <?php $classLinkShared = in_array($controller,array('linkshareds_index','linkshareds_add','linkshareds_edit','linkshareds_delete','linkshareds_search')) ? 'active' : ''; ?>
  <?php $classLivrable = in_array($controller,array('livrables_index','livrables_add','livrables_edit','livrables_delete','livrables_search')) ? 'active' : ''; ?>
  <?php $classCalendardAbs = in_array($controller,array('pages_absences')) ? 'active' : ''; ?>
  <?php $classSections = in_array($controller,array('sections_index','sections_add','sections_edit','sections_delete','sections_search')) ? 'active' : ''; ?> 
  <?php $classSocietes = in_array($controller,array('societes_index','societes_add','societes_edit','societes_delete','societes_search')) ? 'active' : ''; ?> 
  <?php $classSites = in_array($controller,array('sites_index','sites_add','sites_edit','sites_delete','sites_search')) ? 'active' : ''; ?>               
  <?php $classDossierPartages = in_array($controller,array('dossierpartages_index','dossierpartages_add','dossierpartages_edit','dossierpartages_delete','dossierpartages_search')) ? 'active' : ''; ?> 
  <?php $classDomaines = in_array($controller,array('domaines_index','domaines_add','domaines_edit','domaines_delete','domaines_search')) ? 'active' : ''; ?>               
  <?php $classOutils = in_array($controller,array('outils_index','outils_add','outils_edit','outils_delete','outils_search')) ? 'active' : ''; ?>    
  <?php $classAssistances = in_array($controller,array('assistances_index','assistances_add','assistances_edit','assistances_delete','assistances_search')) ? 'active' : ''; ?> 
  <?php $classListeDiffusions = in_array($controller,array('listediffusions_index','listediffusions_add','listediffusions_edit','listediffusions_delete','listediffusions_search')) ? 'active' : ''; ?>               
  <?php $classProfils = in_array($controller,array('profils_index','profils_add','profils_edit','profils_delete','profils_search')) ? 'active' : ''; ?>  
  <?php $classAutorisations = in_array($controller,array('autorisations_index','autorisations_add','autorisations_edit','autorisations_delete','autorisations_search')) ? 'active' : ''; ?>                
  <?php $classTypeMateriels = in_array($controller,array('typemateriels_index','typemateriels_add','typemateriels_edit','typemateriels_delete','typemateriels_search')) ? 'active' : ''; ?> 
  <?php $classMessages = in_array($controller,array('messages_index','messages_add','messages_edit','messages_delete','messages_search')) ? 'active' : ''; ?>
  <?php $classAdministration = in_array('active',array($classSections,$classSocietes,$classSites,$classDossierPartages,$classAutorisations,$classDomaines,$classOutils,$classAssistances,$classProfils,$classListeDiffusions,$classTypeMateriels,$classMessages)) ? 'active' : ''; ?>               
  <?php $classContrats = in_array($controller,array('contrats_index','contrats_add','contrats_edit','contrats_delete','contrats_search')) ? 'active' : ''; ?> 
  <?php $classProjets = in_array($controller,array('projets_index','projets_add','projets_edit','projets_delete','projets_search')) ? 'active' : ''; ?> 
  <?php $classActivites = in_array($controller,array('activites_index','activites_add','activites_edit','activites_delete','activites_search')) ? 'active' : ''; ?>               
  <?php $classTJMProjets = in_array($controller,array('tjmcontrats_index','tjmcontrats_add','tjmcontrats_edit','tjmcontrats_delete','tjmcontrats_search')) ? 'active' : ''; ?> 
  <?php $classTJMAgents = in_array($controller,array('tjmagents_index','tjmagents_add','tjmagents_edit','tjmagents_delete','tjmagents_search')) ? 'active' : ''; ?>               
  <?php $classAchats = in_array($controller,array('achats_index','achats_add','achats_edit','achats_delete','achats_search')) ? 'active' : ''; ?>    
  <?php $classPlanDeCharge = in_array($controller,array('plandecharges_index','plandecharges_add','plandecharges_edit','plandecharges_delete','plandecharges_search')) ? 'active' : ''; ?>      
  <?php $classFacturation = in_array($controller,array('facturations_index','facturations_add','facturations_edit','facturations_delete','facturations_search')) ? 'active' : ''; ?>        
  <?php $classBudget = in_array('active',array($classFacturation,$classPlanDeCharge,$classContrats,$classProjets,$classActivites,$classAchats,$classTJMProjets,$classTJMAgents)) ? 'active' : ''; ?>  
  <?php $classUtilisateurs = in_array($controller,array('utilisateurs_index','utilisateurs_add','utilisateurs_edit','utilisateurs_delete','utilisateurs_search','dotations_add','dotations_edit','dotations_delete','affectations_add','affectations_edit','affectations_delete')) ? 'active' : ''; ?> 
  <?php $classMateriels = in_array($controller,array('materielinformatiques_index','materielinformatiques_add','materielinformatiques_edit','materielinformatiques_delete','materielinformatiques_search')) ? 'active' : ''; ?>               
  <?php $classPetitMateriels = in_array($controller,array('materielautres_index','materielautres_add','materielautres_edit','materielautres_delete','materielautres_search')) ? 'active' : ''; ?>               
  <?php $classUtiliseOutils = in_array($controller,array('utiliseoutils_index','utiliseoutils_add','utiliseoutils_edit','utiliseoutils_delete','utiliseoutils_search')) ? 'active' : ''; ?>    
  <?php $classLogistique = in_array('active',array($classPetitMateriels,$classUtilisateurs,$classMateriels,$classUtiliseOutils)) ? 'active' : ''; ?> 
  <?php $classCRAMois = in_array($controller,array('pages_cramois')) ? 'active' : ''; ?>               
  <?php $classCRAPeriode = in_array($controller,array('pages_craperiode')) ? 'active' : ''; ?>    
  <?php $classRapports = in_array('active',array($classCRAMois,$classCRAPeriode)) ? 'active' : ''; ?> 
  <?php $classContactUs = in_array($controller,array('contacts_add')) ? 'active' : ''; ?>               
  <?php $classAddFavorites = in_array($controller,array('pages_addfavorites')) ? 'active' : ''; ?>  
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display: block; position: static; margin-bottom: 15px; margin-left: -20px; margin-top: 15px;">
    <li class="dropdown-menu-nolink sstitre text-center">6404901Z</li>
    <li class="<?php echo $classProfil; ?>"><?php echo $this->Html->link('<i class="glyphicon_user"></i> Mon profil',array('controller'=>'utilisateurs','action'=>'profil'),array('escape' => false)); ?></li>
    <li class="<?php echo $classLogout; ?>"><?php echo $this->Html->link('<i class="glyphicon_power"></i> Se déconnecter',array('controller'=>'utilisateurs','action'=>'logout'),array('escape' => false)); ?></li>           
    <li class="divider"></li>
    <li class="<?php echo $classHome; ?>"><?php echo $this->Html->link('<i class="glyphicon_home"></i> Accueil',array('controller'=>'pages','action'=>'home'),array('escape' => false)); ?></li>                
    <li class="divider"></li>                
    <li class="<?php echo $classAction; ?>"><?php echo $this->Html->link('<i class="glyphicon_stopwatch"></i> Actions',array('controller'=>'actions','action'=>'index'),array('escape' => false)); ?></li>
    <li class="<?php echo $classActvitereelle; ?>"><?php echo $this->Html->link('<i class="icon-tasks"></i> Feuille de temps',array('controller'=>'activitesreelles','action'=>'index'),array('escape' => false)); ?></li>    
    <li class="<?php echo $classLinkShared; ?>"><?php echo $this->Html->link('<i class="glyphicon_link"></i> Liens partagés',array('controller'=>'linkshareds','action'=>'index'),array('escape' => false)); ?></li>
    <li class="<?php echo $classLivrable; ?>"><?php echo $this->Html->link('<i class="glyphicon_inbox"></i> Livrables',array('controller'=>'livrables','action'=>'index','week','tous','tous'),array('escape' => false)); ?></li>
    <li class="<?php echo $classCalendardAbs; ?>"><?php echo $this->Html->link('<i class="glyphicon_beach_umbrella"></i> Absences équipe',array('controller'=>'pages','action'=>'absences'),array('escape' => false)); ?></li>
    <li class="divider"></li>
    <li  class="dropdown-submenu  <?php echo $classAdministration; ?>"><a href="#"><i class="glyphicon_lock"></i> Administration</a>
        <ul class="dropdown-menu">
            <li class="<?php echo $classMessages; ?>"><?php echo $this->Html->link('Messages',array('controller'=>'messages','action'=>'index'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classProfils; ?>"><?php echo $this->Html->link('Profils',array('controller'=>'profils','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classAutorisations; ?>"><?php echo $this->Html->link('Autorisations',array('controller'=>'autorisations','action'=>'index','tous'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classAssistances; ?>"><?php echo $this->Html->link('Assistances',array('controller'=>'assistances','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classDomaines; ?>"><?php echo $this->Html->link('Domaines',array('controller'=>'domaines','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classListeDiffusions; ?>"><?php echo $this->Html->link('Listes de diffusion',array('controller'=>'listediffusions','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classOutils; ?>"><?php echo $this->Html->link('Outils',array('controller'=>'outils','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classDossierPartages; ?>"><?php echo $this->Html->link('Partages réseaux',array('controller'=>'dossierpartages','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classSections; ?>"><?php echo $this->Html->link('Sections',array('controller'=>'sections','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classSites; ?>"><?php echo $this->Html->link('Sites',array('controller'=>'sites','action'=>'index'),array('escape' => false)); ?></li>                        
            <li class="<?php echo $classSocietes; ?>"><?php echo $this->Html->link('Sociétés',array('controller'=>'societes','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classTypeMateriels; ?>"><?php echo $this->Html->link('Types de matérel',array('controller'=>'typemateriels','action'=>'index'),array('escape' => false)); ?></li>
        </ul>
    </li>
    <li  class="dropdown-submenu <?php echo $classLogistique; ?>"><a href="#"><i class="glyphicon_imac"></i> Logistique</a>
        <ul class="dropdown-menu">
            <li class="<?php echo $classUtilisateurs; ?>"><?php echo $this->Html->link('Utilisateurs',array('controller'=>'utilisateurs','action'=>'index','actif','allsections'),array('escape' => false)); ?></li>
            <li class="<?php echo $classMateriels; ?>"><?php echo $this->Html->link('Postes informatique',array('controller'=>'materielinformatiques','action'=>'index','En stock','tous','toutes'),array('escape' => false)); ?></li>
            <!--<li class="<?php echo $classPetitMateriels; ?>"><?php echo $this->Html->link('Périphériques',array('controller'=>'materielautres','action'=>'index'),array('escape' => false)); ?></li>//-->
            <li class="<?php echo $classUtiliseOutils; ?>"><?php echo $this->Html->link('Ouvertures des droits',array('controller'=>'utiliseoutils','action'=>'index','tous','tous'),array('escape' => false)); ?></li>
            <!--<li class="disabled"><a href="#">Ouvertures des droits</a></li>//-->
        </ul>
    </li>
    <li  class="dropdown-submenu <?php echo $classBudget; ?>"><a href="#"><i class="glyphicon_euro"></i> Budget</a>
        <ul class="dropdown-menu">
            <li class="<?php echo $classContrats; ?>"><?php echo $this->Html->link('Contrats',array('controller'=>'contrats','action'=>'index','actif'),array('escape' => false)); ?></li>
            <li class="<?php echo $classProjets; ?>"><?php echo $this->Html->link('Projets',array('controller'=>'projets','action'=>'index','actif','tous'),array('escape' => false)); ?></li>
            <li class="<?php echo $classActivites; ?>"><?php echo $this->Html->link('Activités',array('controller'=>'activites','action'=>'index','actif','tous'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classAchats; ?>"><?php echo $this->Html->link('Achats',array('controller'=>'achats','action'=>'index','toutes'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classTJMProjets; ?>"><?php echo $this->Html->link('TJM Contrats',array('controller'=>'tjmcontrats','action'=>'index'),array('escape' => false)); ?></li>
            <li class="<?php echo $classTJMAgents; ?>"><?php echo $this->Html->link('TJM Agents',array('controller'=>'tjmagents','action'=>'index'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classPlanDeCharge; ?>"><?php echo $this->Html->link('Plan de charge',array('controller'=>'plandecharges','action'=>'index'),array('escape' => false)); ?></li>
            <li class="divider"></li>
            <li class="<?php echo $classFacturation; ?>"><?php echo $this->Html->link('Facturation',array('controller'=>'facturations','action'=>'index'),array('escape' => false)); ?></li>
        </ul>
    </li>
    <li class="divider"></li>
    <li  class="dropdown-submenu <?php echo $classRapports; ?>"><a href="#"><i class="glyphicon_charts"></i> Rapports</a>
        <ul class="dropdown-menu">
            <li class="<?php echo $classCRAMois; ?>"><?php echo $this->Html->link('Activités - Mensuel',array('controller'=>'pages','action'=>'cramois'),array('escape' => false)); ?></li>
            <li class="<?php echo $classCRAPeriode; ?>"><?php echo $this->Html->link('Activités - Périodique',array('controller'=>'pages','action'=>'craperiode'),array('escape' => false)); ?></li>
        </ul>
    </li>
    <li class="divider"></li>
    <li class="<?php echo $classContactUs; ?>"><?php echo $this->Html->link('<i class="glyphicon_envelope"></i> Nous contacter',array('controller'=>'contacts','action'=>'add'),array('escape' => false)); ?></li>
    <li class="<?php echo $classAddFavorites; ?>"><?php echo $this->Html->link('<i class="glyphicon_star"></i> Ajouter aux favoris',array('controller'=>'pages','action'=>'addfavorites'),array('escape' => false)); ?></li>
    <li  class="dropdown-menu-nolink sstitre text-center">Version : <?php echo $this->element('version') ?></li>
  </ul>
</div>