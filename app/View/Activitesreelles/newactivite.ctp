<?php echo $this->Form->create('Activitesreelle',array('id'=>'formValidate','class'=>'form-horizontal','inputDefaults' => array('error'=>false,'label'=>false,'div' => false))); ?>
<table>
    <tr>
        <td><label class="col-md-4 control-label required" for="ActivitesreelleUtilisateurId">Pour : </label></td>
        <td>
            <?php if (userAuth('WIDEAREA')==1): ?>
            <?php echo $this->Form->select('utilisateur_id',$utilisateurs,array('data-rule-required'=>'true','default' => userAuth('id'),'data-msg-required'=>"Le nom de l'utilisateur est obligatoire", 'empty' => 'Choisir un utilisateur')); ?>                     
            <?php else : ?>
            <?php echo $utilisateur['Utilisateur']['NOMLONG']; ?><?php echo $this->Form->input('utilisateur_id',array('type'=>'hidden','value'=>$utilisateur['Utilisateur']['id'])); ?>
            <?php endif; ?>
        </td>
        <td><label class="col-md-4 control-label required" for="ActivitesreelleDATE">Date début de semaine : </label></td>
        <td>
            <div class="input-append date" data-date="<?php echo empty($this->data['Activitesreelle']['DATE']) ? date('d/m/Y') : $this->data['Activitesreelle']['DATE']; ?>" data-date-format="dd/mm/yyyy">
                <?php $today = date('d/m/Y'); ?>
                <?php echo $this->Form->input('DATE',array('type'=>'text','placeholder'=>'ex.: '.$today,"readonly"=>'true','data-rule-required'=>'true','data-msg-required'=>"La date de début de l'activité est obligatoire",'error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
                <button class="btninput dateremove" type="button" id="remove" name="remove" rel="tooltip" data-title="Effacer la date"><span class="glyphicons circle_remove grey"></span></button>
                <span class="add-on"><span class="glyphicons calendar"></span></span>
            </div>             
        </td>
    </tr>
</table>
<div class="navbar">
    <div class="navbar-inner">
        <div class="container" style="margin-top:2px;text-align:center;">
            <?php echo $this->Form->button('Annuler', array('type'=>'button','class' => 'btn btn-sm showoverlay','onclick'=>"location.href='".goPrev()."'")); ?>&nbsp;<?php echo $this->Form->button('Continuer', array('class' => 'btn btn-sm btn-primary','type'=>'submit')); ?>                  
        </div>
    </div>
</div>  
<?php echo $this->Form->end(); ?>   