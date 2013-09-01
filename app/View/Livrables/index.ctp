<div class="livrables index">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                <ul class="nav">
                <?php if (userAuth('profil_id')!='2' && isAuthorized('livrables', 'add')) : ?>
                <li><?php echo $this->Html->link('<span class="glyphicons plus size14" rel="tooltip" data-title="Ajoutez un livrable"></span>', array('action' => 'add'),array('escape' => false)); ?></li>
                <li class="divider-vertical-only"></li>
                <?php endif; ?>
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Filtre Chronologie<b class="caret"></b></a>
                     <ul class="dropdown-menu">
                     <li><?php echo $this->Html->link('Toutes', array('action' => 'index','toutes',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('En retard', array('action' => 'index','tolate',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('Semaine précédente', array('action' => 'index','previousweek',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li><?php echo $this->Html->link('Semaine courante', array('action' => 'index','week',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li><?php echo $this->Html->link('Semaine suivante', array('action' => 'index','nextweek',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('A venir', array('action' => 'index','otherweek',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     </ul>
                 </li>                   
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Filtre Etat<b class="caret"></b></a>
                     <ul class="dropdown-menu">
                     <li><?php echo $this->Html->link('Tous', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','tous',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('A faire', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','todo',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li><?php echo $this->Html->link('En cours', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','inmotion',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li><?php echo $this->Html->link('Livré', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','delivered',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li><?php echo $this->Html->link('Validé', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','validated',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('Autre que validé', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes','notvalidated',isset($this->params->pass[2]) ? $this->params->pass[2] : 'tous')); ?></li>                     
                     </ul>
                </li> 
                <?php if (areaIsVisible()) :?>
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Filtre Gestionnaire<b class="caret"></b></a>
                     <ul class="dropdown-menu">
                     <li><?php echo $this->Html->link('Tous', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes',isset($this->params->pass[1]) ? $this->params->pass[1] : 'toutes','tous')); ?></li>
                     <li><?php echo $this->Html->link('Moi', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes',isset($this->params->pass[1]) ? $this->params->pass[1] : 'toutes',  userAuth('id'))); ?></li>
                     <li class="divider"></li>
                     <li><?php echo $this->Html->link('Mon équipe', array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes',isset($this->params->pass[1]) ? $this->params->pass[1] : 'toutes',  'equipe')); ?></li>
                     <li class="divider"></li>                    
                     <?php //debug($gestionnaires); ?>
                         <?php foreach ($gestionnaires as $gestionnaire): ?>
                            <li><?php echo $this->Html->link($gestionnaire['Utilisateur']['NOMLONG'], array('action' => 'index',isset($this->params->pass[0]) ? $this->params->pass[0] : 'toutes',isset($this->params->pass[1]) ? $this->params->pass[1] : 'tous',$gestionnaire['Utilisateur']['id'])); ; ?></li>
                         <?php endforeach; ?>
                     </ul>
                </li>   
                <?php endif; ?>
                <li class="divider-vertical-only"></li>
                <li><?php echo $this->Html->link('<span class="ico-xls icon-top2" rel="tooltip" data-title="Export Excel"></span>', array('action' => 'export_xls'),array('escape' => false)); ?></li>
                </ul> 
                <?php echo $this->Form->create("Livrable",array('action' => 'search','class'=>'navbar-form clearfix pull-right','inputDefaults' => array('label'=>false,'div' => false))); ?>
                    <?php echo $this->Form->input('SEARCH',array('placeholder'=>'Recherche dans tous les champs')); ?>
                    <button type="submit" class="btn">Rechercher</button>
                <?php echo $this->Form->end(); ?>
                <ul class="nav pull-right">
                    <li><a href="#" rel="popover" data-title="Aide" data-placement="bottom" data-content="<?php echo $this->element('hlp/hlp-livrables'); ?>"><span><span class="glyphicons blue circle_question_mark size14"></span></span></a></li>
                </ul>                       
                </div>
            </div>
        </div>
        <?php if($this->params['action']=='index') { ?><code class="text-normal"  style="margin-bottom: 10px;display: block;"><em>Liste de <?php echo $fchronologie; ?>, <?php echo $fetat; ?> et <?php echo $fgestionnaire; ?></em></code><?php } ?>
        <table cellpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">
        <thead>
	<tr>
			<th><?php echo $this->Paginator->sort('NOM','Nom'); ?></th>
                        <th><?php echo $this->Paginator->sort('NOMLONG','Nom du gestionnaire'); ?></th>
			<th width="60px"><?php echo $this->Paginator->sort('REFERENCE','Réf. MINIDOC'); ?></th>
			<th width="40px"><?php echo $this->Paginator->sort('VERSION','Version'); ?></th>
                        <th width="40px"><?php echo $this->Paginator->sort('ETAT','Etat'); ?></th>
			<th width="90px"><?php echo $this->Paginator->sort('ECHEANCE','Echéance'); ?></th>
			<th width="90px"><?php echo $this->Paginator->sort('DATELIVRAISON','Date de livraison'); ?></th>                        
			<th width="75px" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
        </thead>
        <tbody>        
	<?php if (isset($livrables)): ?>
	<?php foreach ($livrables as $livrable): ?>
	<tr>
		<td><?php echo h($livrable['Livrable']['NOM']); ?>&nbsp;</td>
                <td><?php echo h($livrable['Utilisateur']['NOMLONG']); ?>&nbsp;</td>
                <?php $urlminidoc = $this->requestAction('parameters/get_minidocurl'); ?>
                <?php $urlreference = !empty($urlminidoc['Parameter']['param']) ? $urlminidoc['Parameter']['param'].$livrable['Livrable']['REFERENCE'] : '#'; ?>
		<td style="text-align: center;"><?php echo $this->Html->link(h($livrable['Livrable']['REFERENCE']),$urlreference,array('target'=>'blank')); ?>&nbsp;</td>
                <td style="text-align: center;"><?php echo h($livrable['Livrable']['VERSION']); ?>&nbsp;</td>
                <td style="text-align: center;"><?php echo isset($livrable['Livrable']['ETAT']) ? '<span class="glyphicons '.etatLivrable(h($livrable['Livrable']['ETAT'])).'" rel="tooltip" data-title="'.h($livrable['Livrable']['ETAT']).'"></span>' : '' ; ?></td>
                <?php $classtd = livrableenretard($livrable['Livrable']['ECHEANCE'],$livrable['Livrable']['DATELIVRAISON'],$livrable['Livrable']['ETAT']) ? "class='td-error'" : ""; ?>
		<td <?php echo $classtd; ?> style="text-align: center;"><?php echo h(isset($livrable['Livrable']['ECHEANCE']) ? $livrable['Livrable']['ECHEANCE'] : ''); ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo h(isset($livrable['Livrable']['DATELIVRAISON']) ? $livrable['Livrable']['DATELIVRAISON'] : ''); ?>&nbsp;</td>
		<td class="actions">
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('livrables', 'view')) : ?>
                    <?php echo '<span><span rel="tooltip" data-title="Cliquez pour avoir un aperçu"><span class="glyphicons eye_open" rel="popover" data-title="<h3>Livrable :</h3>" data-content="<contenttitle>Commentaire: </contenttitle>'.h($livrable['Livrable']['COMMENTAIRE']).'<br/><contenttitle>Validé le: </contenttitle>'.h(isset($livrable['Livrable']['DATEVALIDATION']) ? $livrable['Livrable']['DATEVALIDATION'] : '').'<br/><contenttitle>Crée le: </contenttitle>'.h($livrable['Livrable']['created']).'<br/><contenttitle>Modifié le: </contenttitle>'.h($livrable['Livrable']['modified']).'" data-trigger="click" style="cursor: pointer;"></span></span></span>'; ?>&nbsp;
                    <?php endif; ?>
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('livrables', 'edit')) : ?>
                    <?php echo $this->Html->link('<span class="glyphicons pencil" rel="tooltip" data-title="Modification"></span>', array('action' => 'edit', $livrable['Livrable']['id']),array('escape' => false)); ?>&nbsp;
                    <?php endif; ?>
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('livrables', 'delete')) : ?>
                    <?php echo $this->Form->postLink('<span class="glyphicons bin" rel="tooltip" data-title="Suppression"></span>', array('action' => 'delete', $livrable['Livrable']['id']),array('escape' => false), __('Etes-vous certain de vouloir supprimer ce livrables ?')); ?>
                    <?php endif; ?>
                    <?php if (userAuth('profil_id')!='2' && isAuthorized('livrables', 'duplicate')) : ?>
                    <?php echo $this->Form->postLink('<span class="glyphicons retweet" rel="tooltip" data-title="Duplication"></span>', array('action' => 'dupliquer', $livrable['Livrable']['id']),array('escape' => false), __('Etes-vous certain de vouloir créer une nouvelle version de ce livrable ?')); ?>
                    <?php endif; ?>                    
		</td>
	</tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
	</table>
	<div class="pull-left"><?php echo $this->Paginator->counter('Page {:page} sur {:pages}'); ?></div>
	<div class="pull-right"><?php echo $this->Paginator->counter('Nombre total d\'éléments : {:count}'); ?></div>    
	<div class="pagination pagination-centered">
        <ul>
	<?php
                echo "<li>".$this->Paginator->first('<<', true, null, array('class' => 'disabled showoverlay'))."</li>";
		echo "<li>".$this->Paginator->prev('<', array(), null, array('class' => 'prev disabled showoverlay'))."</li>";
		echo "<li>".$this->Paginator->numbers(array('separator' => '','class'=>'showoverlay'))."</li>";
		echo "<li>".$this->Paginator->next('>', array(), null, array('class' => 'disabledshowoverlay'))."</li>";
                echo "<li>".$this->Paginator->last('>>', true, null, array('class' => 'disabled showoverlay'))."</li>";
	?>
        </ul>
	</div>
</div>