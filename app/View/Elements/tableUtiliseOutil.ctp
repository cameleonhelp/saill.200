 <?php
 /**
 * etatMaterielInformatiqueImage method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $etat
 * @return string class
 */  
        function etatUtiliseOutilImage($etat) {
            $class = '';
            switch ($etat){
                 case 'Demandé':
                    $class = 'icon-envelope';
                    break;
                 case 'Pris en compte':
                    $class = 'icon-flag';
                    break;                
                 case 'En validation':
                    $class = 'icon-bookmark icon-grey';
                    break;          
                 case 'Validé':
                    $class = 'icon-bookmark icon-green';
                    break;
                 case 'Demande transférée':
                    $class = 'icon-share-alt';
                    break;                
                 case 'Demande traitée':
                    $class = 'icon-ok';
                    break;
                 case 'Retour utilisateur':
                    $class = 'icon-ok icon-green';
                    break;                
                 case 'A supprimer':
                    $class = 'icon-remove';
                    break;          
                 case 'Supprimée':
                    $class = 'icon-remove icon-red';
                    break; 
            }
            return $class;
        } 
?> 
<br/>
<div class="utiliseoutils index">
	<table cellpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">
        <thead>
	<tr>
			<th><?php echo 'Outils'; ?></th>
			<th><?php echo 'Liste de diffusion'; ?></th>
                        <th><?php echo 'Partage'; ?></th>
                        <th width='70px'><?php echo 'Etat de la demande'; ?></th>
	</tr>
        </thead>
        <tbody>
	<?php foreach ($utiliseoutils as $utiliseoutil): ?>
	<tr>
		<td><?php echo h($utiliseoutil['Outil']['NOM']); ?>&nbsp;</td>
                <td><?php echo h($utiliseoutil['Listediffusion']['NOM']); ?>&nbsp;</td>
                <td><?php echo h($utiliseoutil['Dossierpartage']['NOM']); ?>&nbsp;</td>
		<td style='text-align:center;'><i class="<?php echo etatUtiliseOutilImage(h($utiliseoutil['Utiliseoutil']['STATUT'])); ?>" rel="tooltip" data-title="<?php echo h(h($utiliseoutil['Utiliseoutil']['STATUT'])); ?>"></i>&nbsp;</td>
	</tr>
<?php endforeach; ?>
        </tbody>
	</table>
    </div>