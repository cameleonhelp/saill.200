<div class="contrats index">
        <nav class="navbar toolbar marginright20">
                <ul class="nav navbar-nav toolbar">
                <?php if (userAuth('profil_id')!='2' && isAuthorized('contrats', 'add')) : ?>
                <li><?php echo $this->Html->link('<span class="glyphicons plus size14 margintop4"></span>', array('action' => 'add'),array('escape' => false,'class'=>'showoverlay')); ?></li>
                <li class="divider-vertical-only"></li>
                <?php endif; ?>
                <li class="dropdown <?php echo filtre_is_actif(isset($this->params->pass[0]) ? $this->params->pass[0] : 'tous','tous'); ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Filtre Etats <b class="caret"></b></a>
                     <ul class="dropdown-menu">
                         <li><?php echo $this->Html->link('Tous', array('action' => 'index','tous'),array('class'=>'showoverlay'.subfiltre_is_actif(isset($this->params->pass[0]) ? $this->params->pass[0] : 'tous','tous'))); ?></li>
                         <li class="divider"></li>
                         <li><?php echo $this->Html->link('Actif', array('action' => 'index','actif'),array('class'=>'showoverlay'.subfiltre_is_actif(isset($this->params->pass[0]) ? $this->params->pass[0] : 'tous','actif'))); ?></li>
                         <li><?php echo $this->Html->link('Inactif', array('action' => 'index','inactif'),array('class'=>'showoverlay'.subfiltre_is_actif(isset($this->params->pass[0]) ? $this->params->pass[0] : 'tous','inactif'))); ?></li>
                     </ul>
                 </li>                 
                </ul> 
                <?php echo $this->Form->create("Contrat",array('action' => 'search', 'class'=>'toolbar-form pull-right','inputDefaults' => array('error'=>false,'label'=>false,'div' => false))); ?>
                    <?php echo $this->Form->input('SEARCH',array('placeholder'=>'Recherche ...','style'=>"width: 200px;",'class'=>"form-control")); ?>
                    <button type="submit" class="btn form-btn showoverlay">Rechercher</button>
                <?php echo $this->Form->end(); ?> 
        </nav>
        <?php if ($this->params['action']=='index') { ?><div class="panel-body panel-filter marginbottom15 marginright20"><strong>Filtre appliqué : </strong><em>Liste de <?php echo $fcontrat; ?></em></div><?php } ?>        
        <div class="marginright10">
        <table cellpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">
        <thead>
	<tr>
			<th><?php echo $this->Paginator->sort('NOM','Nom'); ?></th>
                        <th width="60px;"><?php echo $this->Paginator->sort('tjmcontrat_id','TJM moyen'); ?></th>
			<th width="60px;"><?php echo $this->Paginator->sort('ANNEEDEBUT','Début'); ?></th>
			<th width="60px;"><?php echo $this->Paginator->sort('ANNEEFIN','Fin'); ?></th>
			<!--<th><?php echo $this->Paginator->sort('MONTANT','Montant en k€'); ?></th>//-->
			<th width="40px;"><?php echo $this->Paginator->sort('ACTIF','Actif'); ?></th>
			<th class="actions" width="60px;"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
        <tbody>
	<?php if (isset($contrats)): ?>
	<?php foreach ($contrats as $contrat): ?>
	<tr>
		<td><?php echo h($contrat['Contrat']['NOM']); ?>&nbsp;</td>
                <td style='text-align: center;'><?php echo h($contrat['Tjmcontrat']['TJM']); ?>&nbsp;</td>
		<td style='text-align: center;'><?php echo h($contrat['Contrat']['ANNEEDEBUT']); ?>&nbsp;</td>
		<td style='text-align: center;'><?php echo h($contrat['Contrat']['ANNEEFIN']); ?>&nbsp;</td>
		<!--<td style='text-align: right;'><?php echo h($contrat['Contrat']['MONTANT']); ?> k€&nbsp;</td>//-->
		<td style='text-align: center;'><?php echo $contrat['Contrat']['ACTIF']==1 ? '<span class="glyphicons ok_2"></span>' : ''; ?>&nbsp;</td>
		<td class="actions">
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('contrats', 'view')) : ?>
                    <?php echo '<span class="glyphicons eye_open" data-rel="popover" data-title="<h3>Contrat :</h3>" data-content="<contenttitle>Crée le: </contenttitle>'.h($contrat['Contrat']['created']).'<br/><contenttitle>Modifié le: </contenttitle>'.h($contrat['Contrat']['modified']).'" data-trigger="click" style="cursor: pointer;"></span>'; ?>&nbsp;
                    <?php endif; ?>
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('contrats', 'edit')) : ?>        
                    <?php echo $this->Html->link('<span class="glyphicons pencil showoverlay notchange"></span>', array('action' => 'edit', $contrat['Contrat']['id']),array('escape' => false,'class'=>'showoverlay')); ?>&nbsp;
		    <?php endif; ?>
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('contrats', 'delete')) : ?>
                    <?php echo $contrat['Tjmcontrat']['id']>1 ? $this->Form->postLink('<span class="glyphicons bin notchange"></span>', array('action' => 'delete', $contrat['Contrat']['id']),array('escape' => false), __('Etes-vous certain de vouloir supprimer ce contrat ?')):''; ?>
                    <?php endif; ?>
                </td>
	</tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
	</table>
        </div>
        <div class="pull-left">	<?php	echo $this->Paginator->counter('Page {:page} sur {:pages}');	?></div>
        <div class="pull-right marginright20"><?php	echo $this->Paginator->counter('Nombre total d\'éléments : {:count}');	?></div>
        <div class='text-center'>
        <ul class="pagination pagination-sm">
	<?php
                echo "<li>".$this->Paginator->first('<<', true, null, array('class' => 'disabled showoverlay','escape'=>false))."</li>";
		echo "<li>".$this->Paginator->prev('<', array(), null, array('class' => 'prev disabled showoverlay','escape'=>false))."</li>";
		echo "<li>".$this->Paginator->numbers(array('separator' => '','class'=>'showoverlay'))."</li>";
		echo "<li>".$this->Paginator->next('>', array(), null, array('class' => 'disabled showoverlay','escape'=>false))."</li>";
                echo "<li>".$this->Paginator->last('>>', true, null, array('class' => 'disabled showoverlay','escape'=>false))."</li>";
	?>
        </ul>
        </div>
</div>
