<div class="">
<?php echo $this->Form->create('Livrable',array('id'=>'formValidate','class'=>'form-horizontal','inputDefaults' => array('error'=>false,'label'=>false,'div' => false))); ?>
    <div class="form-group">
        <label class="col-md-2 required" for="LivrableUtilisateurId">Gestionnaire du livrable : </label>
        <div class="col-md-6">  
            <?php if ($this->params->action == 'add') : ?>
            <?php if (userAuth('WIDEAREA')==1): ?>
            <?php echo $this->Form->select('utilisateur_id',$utilisateur,array('class'=>'form-control','data-rule-required'=>'true','default' => userAuth('id'),'data-msg-required'=>'Le nom du gestionnaire est obligatoire','empty' => 'Choisir un gestionnaire')); ?>
            <?php else : ?>
            <?php echo $nomlong; ?>
            <?php echo $this->Form->input('utilisateur_id',array('type'=>'hidden','value'=>userAuth('id'))); ?>
            <?php endif; ?>            
            <?php else : ?>
            <?php if (userAuth('WIDEAREA')==1): ?>
            <?php echo $this->Form->select('utilisateur_id',$utilisateur,array('class'=>'form-control','data-rule-required'=>'true','data-msg-required'=>'Le nom du gestionnaire est obligatoire','empty' => 'Choisir un gestionnaire')); ?>
            <?php else : ?>
            <?php echo $nomlong; ?>
            <?php echo $this->Form->input('utilisateur_id',array('type'=>'hidden','value'=>userAuth('id'))); ?>
            <?php endif; ?>              
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2  required" for="LivrableNOM">Nom du livrable: </label>
        <div class="col-md-6">
            <?php echo $this->Form->input('NOM',array('class'=>'form-control','data-rule-required'=>'true','placeholder'=>'Nom de livrable','data-msg-required'=>'Le nom de livrable est obligatoire','error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2" for="LivrableREFERENCE">Référence MINIDOC  : </label>
        <div class="col-md-2">
            <?php echo $this->Form->input('REFERENCE',array('class'=>'form-control','placeholder'=>'Réf. MINIDOC','error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2" for="LivrableVERSION">Version  : </label>
        <div class="col-md-2">
            <?php echo $this->Form->input('VERSION',array('class'=>'form-control','placeholder'=>'Version','error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
        </div>
    </div>
    <!--onglets pour les reste des informations-->
    <ul class="nav nav-tabs" id="tabdetail">
        <li class="active"><a href="#detail" data-toggle="tab">Détails</a></li>
      <li><a href="#commentaire" data-toggle="tab">Commentaire</a></li>
      <?php if ($this->params['action']=='edit'){ ?><li><a href="#history" data-toggle="tab">Historique</a></li><?php } ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="detail"><p>
            <div class='form-group'>
                <label class="col-md-2 required" for="LivrableECHEANCE">Echéance de livraison</label>
                <div class="col-md-3">
                    <div class="input-group" style="margin-left: 0px;margin-right: 0px;">
                    <?php $today = new dateTime(); ?>
                    <?php echo $this->Form->input('ECHEANCE',array('type'=>'text','placeholder'=>'ex.: '.$today->format('d/m/Y'),'data-rule-required'=>'true','class'=>"form-control dateall",'data-msg-required'=>"L'échéance de livraison est obligatoire",'error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
                    <span class="input-group-addon addon-middle date-addon-clean btn-addon" data-target="#LivrableECHEANCE"><span class="glyphicons circle_remove grey"></span></span>
                    <span class="input-group-addon date-addon-calendar btn-addon" data-target="#LivrableECHEANCE"><span class="glyphicons calendar"></span></span>
                    </div>              
                </div>
            </div>  
            <div class='form-group'>
                <label class="col-md-2" for="LivrableDATEVALIDATION">Date de validation : </label>
                <div class="col-md-3">
                    <div class="input-group" style="margin-left: 0px;margin-right: 0px;">
                    <?php $today = new dateTime(); ?>
                    <?php echo $this->Form->input('DATEVALIDATION',array('type'=>'text','placeholder'=>'ex.: '.$today->format('d/m/Y'),'class'=>"form-control dateall",'error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
                    <span class="input-group-addon addon-middle date-addon-clean btn-addon" data-target="#LivrableDATEVALIDATION"><span class="glyphicons circle_remove grey"></span></span>
                    <span class="input-group-addon date-addon-calendar btn-addon" data-target="#LivrableDATEVALIDATION"><span class="glyphicons calendar"></span></span>
                    </div>            
                </div>
            </div>
            <div class='form-group'>
                <label class="col-md-2" for="LivrableDATELIVRAISON">Livraison</label>
                <div class="col-md-3">
                    <div class="input-group" style="margin-left: 0px;margin-right: 0px;">
                    <?php $today = new dateTime(); ?>
                    <?php echo $this->Form->input('DATELIVRAISON',array('type'=>'text','placeholder'=>'ex.: '.$today->format('d/m/Y'),'data-rule-frdateisgreater'=>'#LivrableDATEVALIDATION','data-msg-frdateisgreater'=>"La livraison doit être postérieure à la validation.",'class'=>"form-control dateall",'error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?>
                    <span class="input-group-addon addon-middle date-addon-clean btn-addon" data-target="#LivrableDATELIVRAISON"><span class="glyphicons circle_remove grey"></span></span>
                    <span class="input-group-addon date-addon-calendar btn-addon" data-target="#LivrableDATELIVRAISON"><span class="glyphicons calendar"></span></span>
                    </div>  
                </div>
            </div>  
            <div class="form-group">
                <label class="col-md-2 required" for="LivrableETAT">Etat du livrable : </label>
                <div class="col-md-3">
                        <?php echo $this->Form->select('ETAT',$etats,array('class'=>'form-control','data-rule-required'=>'true','data-msg-required'=>"L'état du livrable est obligatoire",'selected' => '','empty' => 'Choisir un état')); ?>
                </div>
            </div>            
        </p></div>
        <div class="tab-pane fade" id="commentaire"><p><?php echo $this->Form->input('COMMENTAIRE',array('type'=>'textarea','error' => array('attributes' => array('wrap' => 'span', 'style' => 'display:none;')))); ?></p></div>
        <?php if ($this->params['action']=='edit'){ ?><div class="tab-pane fade" id="history"><p><?php echo $this->element('tableSuiviLivrable'); ?></p></div><?php } ?>
    </div>
    <!--fin des onglets pour le reste des informations-->
    <div style="clear:both;margin-top: 10px;">
    <div class="form-group">
      <div class="btn-block-horizontal">
            <?php $url = $this->Html->url(array('controller'=>'activitesreelles','action'=>'index','tous',userAuth('id'),date('m'))); ?>
            <?php echo $this->Form->button('Annuler', array('type'=>'submit','class' => 'btn btn-sm btn-default showoverlay cancel','value'=>'cancel','div' => false, 'name' => 'cancel')); ?>&nbsp;<?php echo $this->Form->button('Enregistrer', array('class' => 'btn btn-sm btn-primary','type'=>'submit')); ?>                
      </div>
    </div>  
    </div> 
<?php if ($this->params->action == 'edit') echo $this->Form->input('id',array('type'=>'hidden')); ?>    
<?php echo $this->Form->end(); ?>
</div>