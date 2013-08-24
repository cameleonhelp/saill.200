<table cellpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">
<thead>
<tr>
                <th width="18px;">&nbsp;</th>
                <th>Nom du fichier</th>
                <th>Date de modification</th>
                <th>Taille</th>
                <th colspan="2" width="18px;">&nbsp;</th>
</tr>
</thead>
<tbody>    
<?php aarsort($files,'created'); ?>
<?php foreach ($files as $file): ?>
<tr>
        <td style="text-align:center;"><span class="ico-file">&nbsp;</span></td>
        <td><?php echo $file['name']; ?></td>
        <td style="text-align:center;"><?php echo $file['created']; ?></td>
        <td style="text-align:right;"><?php echo $file['size']; ?></td>
        <?php 
            $nfile = new files_folder();
            $file = $file['file'];
            if ($nfile->iswindows()):
                $file = str_replace("\\", "-", $file);
                $file = str_replace(":", "¤", $file);               
            else:
                $file = str_replace('/', '-', $file);                    
            endif;
        ?>
        <td style="text-align:center;"><?php echo $this->Html->link('<span class="glyphicons bin"></span>',array('action'=>'deletebackup',$file),array('escape' => false), __('Etes-vous certain de vouloir supprimer cette sauvegarde.')); ?>&nbsp;</td>
        <td style="text-align:center;"><?php echo $this->Html->link('<span class="glyphicons download"></span>',array('action'=>'restorebdd',$file),array('escape' => false), __('Etes-vous certain de vouloir restaurer cette sauvegarde.')); ?>&nbsp;</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>