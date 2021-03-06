<div class="historyactions index">
	<h2><?php echo __('Historyactions'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('action_id'); ?></th>
			<th><?php echo $this->Paginator->sort('AVANCEMENT'); ?></th>
			<th><?php echo $this->Paginator->sort('DEBUT'); ?></th>
			<th><?php echo $this->Paginator->sort('DEBUTREELLE'); ?></th>
			<th><?php echo $this->Paginator->sort('ECHEANCE'); ?></th>
			<th><?php echo $this->Paginator->sort('CHARGEPREVUE'); ?></th>
			<th><?php echo $this->Paginator->sort('CHARGEREELLE'); ?></th>
			<th><?php echo $this->Paginator->sort('PRIORITE'); ?></th>
			<th><?php echo $this->Paginator->sort('STATUT'); ?></th>
			<th><?php echo $this->Paginator->sort('COMMENTAIRE'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($historyactions as $historyaction): ?>
	<tr>
		<td><?php echo h($historyaction['Historyaction']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($historyaction['Action']['id'], array('controller' => 'actions', 'action' => 'view', $historyaction['Action']['id'])); ?>
		</td>
		<td><?php echo h($historyaction['Historyaction']['AVANCEMENT']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['DEBUT']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['DEBUTREELLE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['ECHEANCE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['CHARGEPREVUE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['CHARGEREELLE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['PRIORITE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['STATUT']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['COMMENTAIRE']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['created']); ?>&nbsp;</td>
		<td><?php echo h($historyaction['Historyaction']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $historyaction['Historyaction']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $historyaction['Historyaction']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $historyaction['Historyaction']['id']), null, __('Are you sure you want to delete # %s?', $historyaction['Historyaction']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Historyaction'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Actions'), array('controller' => 'actions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action'), array('controller' => 'actions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Utilisateurs'), array('controller' => 'utilisateurs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Utilisateur'), array('controller' => 'utilisateurs', 'action' => 'add')); ?> </li>
	</ul>
</div>
