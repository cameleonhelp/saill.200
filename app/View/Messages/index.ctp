<div class="messages index">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                <ul class="nav">
                <li><?php echo $this->Html->link('<i class="icon-plus"></i>', array('action' => 'add'),array('escape' => false)); ?></li>
                <li class="divider-vertical"></li>
                <li><a href="#"><i class="ico-xls"></i></a></li>
                </ul> 
                </div>
            </div>
        </div>
        <table cellpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">
        <thead>
	<tr>
			<th><?php echo $this->Paginator->sort('LIBELLE','Message'); ?></th>
			<th width="140px;"><?php echo $this->Paginator->sort('DATELIMITE','Date de fin de validité'); ?></th>
			<th class="actions" width="60px;"><?php echo __('Actions'); ?></th>
	</tr>
        </thead>
        <tbody>        
	<?php foreach ($messages as $message): ?>
	<tr>
		<td><?php echo $message['Message']['LIBELLE']; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo h($message['Message']['DATELIMITE']); ?>&nbsp;</td>
		<td class="actions">
                        <?php echo '<i class="icon-eye-open" rel="popover" data-title="<h3>Message :</h3>" data-content="<contenttitle>Crée le: </contenttitle>'.h($message['Message']['created']).'<br/><contenttitle>Modifié le: </contenttitle>'.h($message['Message']['modified']).'" style="cursor: pointer;"></i>'; ?>&nbsp;
			<?php echo $this->Html->link('<i class="icon-pencil"></i>', array('action' => 'edit', $message['Message']['id']),array('escape' => false)); ?>&nbsp;
			<?php echo $this->Form->postLink('<i class="icon-trash"></i>', array('action' => 'delete', $message['Message']['id']),array('escape' => false), __('Etes-vous certain de vouloir supprimer ce message ?')); ?>
		</td>
	</tr>
<?php endforeach; ?>
        </tbody>
	</table>
        <div class="pull-left">	<?php	echo $this->Paginator->counter('Page {:page} sur {:pages}');	?></div>
        <div class="pull-right"><?php	echo $this->Paginator->counter('Nombre total d\'éléments : {:count}');	?></div>
        <div class="pagination  pagination-centered">
        <ul>
	<?php
                echo "<li>".$this->Paginator->first('<<', true, null, array('class' => 'disabled'))."</li>";
		echo "<li>".$this->Paginator->prev('<', array(), null, array('class' => 'prev disabled'))."</li>";
		echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
		echo "<li>".$this->Paginator->next('>', array(), null, array('class' => 'disabled'))."</li>";
                echo "<li>".$this->Paginator->last('>>', true, null, array('class' => 'disabled'))."</li>";
	?>
        </ul>
	</div>

</div>
