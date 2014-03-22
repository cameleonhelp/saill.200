<?php $this->set('title_for_layout','Calendrier des absences'); ?>
<div class="">
<table class="table table-bordered table-striped table-hover" id="capture">
        <thead>
            <?php
            $maxday = isset($this->data['Activitesreelle']['month']) ? date('t',strtotime($this->data['Activitesreelle']['month']))+1 : date('t')+1;
            $month = isset($this->data['Activitesreelle']['month']) ? date('m',strtotime($this->data['Activitesreelle']['month'])) :date('m');
            $year = isset($this->data['Activitesreelle']['month']) ? date('Y',strtotime($this->data['Activitesreelle']['month'])) : date('Y');
            $pass = isset($this->data['Activitesreelle']['pass']) ? $this->data['Activitesreelle']['pass'] : '0';
            $strMonth = array('01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril','05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Août','09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
            ?>
            <?php echo $this->Form->create('Activitesreelle',array('action' => 'absences','style'=>'display:none;','inputDefaults' => array('error'=>false,'label'=>false,'div' => false))); ?>
                <tr>
                    <th colspan="<?php echo ($maxday*2)+1; ?>" class="text-center">
                        <div class="btn-group pull-left">
                            <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                              Filtre ...
                              <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header uppercase" style="text-align:left;">Utilisateurs</li>
                                <li style="text-align:left;"><?php echo $this->Html->link('Tous', "#",array('class'=>'showoverlay','id'=>"all")); ?></li>
                                <li style="text-align:left;"><?php echo $this->Html->link('Mon équipe', "#",array('class'=>'showoverlay','id'=>"team")); ?></li>
                                <li class="divider"></li>
                                <li class="dropdown-header uppercase" style="text-align:left;">Cercles</li>
                                <?php foreach ($cercles as $cercle): ?>
                                   <li style="text-align:left;"><?php echo $this->Html->link($cercle['Entite']['NOM'],'#',array('class'=>'showoverlay cercle','data-id'=>$cercle['Entite']['id'])); ?></li>
                                <?php endforeach; ?>                                
                            </ul>
                        </div>
                        <?php echo $this->Form->button('<span class="glyphicons left_arrow" data-container="body" rel="tooltip" data-title="Mois précédent"></span>', array('id'=>"previousMonth",'type'=>'button','class' => 'btn  btn-sm btn-default','style'=>'margin-right:75px;')); ?>
                            <?php echo $strMonth[$month]." ".$year; ?>
                        <?php echo $this->Form->button('<span class="glyphicons right_arrow" data-container="body" rel="tooltip" data-title="Mois suivant"></span>', array('id'=>"nextMonth",'type'=>'button','class' => 'btn btn-sm btn-default','style'=>'margin-left:75px;')); ?>
                        <?php echo $this->Form->button('<span class="glyphicons clock" data-container="body" rel="tooltip" data-title="Mois courant"></span>', array('id'=>"today",'type'=>'button','class' => 'btn  btn-sm btn-default pull-right')); ?>
                        <?php echo $this->Form->button('<span class="glyphicons camera" data-container="body" rel="tooltip" data-title="Enregistrez au format PNG"></span>', array('id'=>"canvas",'type'=>'button','class' => 'btn  btn-sm btn-default pull-right')); ?>
                    </th>
                </tr>
            <?php $day = new DateTime(); $date = isset($this->data['Activitesreelle']['month']) ? $this->data['Activitesreelle']['month'] : $day->format('Y-m-d'); ?>
            <?php echo $this->Form->input('month',array('type'=>'hidden','value'=>$date)); ?>
            <?php echo $this->Form->input('pass',array('type'=>'hidden','value'=>$pass)); ?>
            <?php echo $this->Form->end(); ?>
            <tr>
            <th class="nowrap" style="vertical-align: middle;" rowspan="2">Nom</th>
            <?php 
            $today = new DateTime();
            for($i=1;$i<$maxday;$i++) {
                $nbday = date("N", mktime(0, 0, 0, $month, $i, $year))-1;
                $day = $i<10 ? '0'.$i : $i;                
                $date= new DateTime($year.'-'.$month.'-'.$day);
                $classferie = isFerie($date) ? ' ferie' : '';   
                $interval = $date->format('Ymd') == $today->format('Ymd');
                $class_today = $interval ? " cel-today" : '';
                $strday = array("Lu","Ma","Me","Je","Ve","Sa","Di");
                $weekend = $date->format('N');
                $class = $weekend >5 ? "class='absday week text-center nowrap" : "class='absday text-center nowrap";
                echo "<th colspan='2' ".$class.$classferie.$class_today."'>".$strday[$nbday]."</th>";
            }
            ?>
            </tr>
            <tr>
            <?php 
            for($i=1;$i<$maxday;$i++) {
                $day = $i<10 ? '0'.$i : $i;
                $date=new DateTime($year.'-'.$month.'-'.$day);
                $classferie = isFerie($date) ? ' ferie' : '';
                $interval = $date->format('Ymd') == $today->format('Ymd');
                $class_today = $interval ? " cel-today" : '';              
                $weekend = $date->format('N');
                $class = $weekend >5 ? "class='absday week nowrap text-center" : "class='absday nowrap text-center";
                echo "<th colspan='2' ".$class.$classferie.$class_today."'>".$day."</th>";
            }
            ?>
            </tr>
        
        </thead>
        <tbody>
            <?php foreach($utilisateurs as $utilisateur) : ?>
            <tr class="thin-height">
                <td class="nowrap" style="max-width:120px !important;width:120px !important;min-width:120px !important;"><div  data-container="body" rel="tooltip" data-title="<?php echo $utilisateur['Utilisateur']['NOMLONG']." (".$utilisateur['Utilisateur']['username'].")"; ?>"><?php echo substr($utilisateur['Utilisateur']['PRENOM'],0,1).". ".$utilisateur['Utilisateur']['NOM']; ?></div></td>
            <?php
            $debutactif = CIntDateDeb($utilisateur['Utilisateur']['DATEDEBUTACTIF']); 
            $debutinactif = CIntDateFin($utilisateur['Utilisateur']['FINMISSION']);
            for ($i=1; $i<$maxday; $i++):
                $absences = listIndispo($indisponibilites);                
                $day = $i<10 ? '0'.$i : $i;
                $date=new DateTime($year.'-'.$month.'-'.$day);
                $weekend = $date->format('N');
                $classweek = $weekend >5 ?  ' week': '';              
                $class = "class='absday nowrap";
                $classferie = isFerie($date) ? ' ferie' : '';
                $interval = $date->format('Ymd') == $today->format('Ymd');
                $class_today = $interval ? " sur-today" : '';                 
                if($debutactif > CIntDate($date->format('d/m/Y')) || $debutinactif < CIntDate($date->format('d/m/Y'))):
                    $classIndispo = ' indispo';
                    echo "<td ".$class.$classIndispo.$classweek.$classferie."' style='line-height: 4px;'></td><td ".$class.$classIndispo.$classweek.$classferie."'></td>";
                else :
                    if(is_date_utilisateur_in_array($date->format('Y-m-d'),$utilisateur['Utilisateur']['id'],$absences)):
                        $result = nb_periode($date->format('Y-m-d'),$utilisateur['Utilisateur']['id'],$absences);
                        if (substr($result['nb'],2,1)=='0') {
                            $classIndispo1 = $result['tmp'] ? ' tmpindispo' : ' indispo';
                            $classIndispo2 = $result['tmp'] ? ' tmpindispo' : ' indispo';
                        }                
                        if (substr($result['nb'],2,1)=='5' && $result['periode']) {
                            $classIndispo1 = $result['tmp'] ? ' tmpindispo' : ' indispo';
                            $classIndispo2 = '';
                        }
                        if (substr($result['nb'],2,1)=='5' && !($result['periode'])) {
                            $classIndispo1 = '';
                            $classIndispo2 = $result['tmp'] ? ' tmpindispo' : ' indispo';
                        }            
                        echo "<td ".$class.$classweek.$classferie.$class_today.$classIndispo1."' style='line-height: 4px;'></td><td ".$class.$classweek.$classferie.$class_today.$classIndispo2."'></td>";
                    else:
                        echo "<td ".$class.$classweek.$classferie.$class_today."' style='line-height: 4px;'></td><td ".$class.$classweek.$classferie.$class_today."'></td>";               
                    endif; 
                endif;
            endfor;
            ?>
            </tr>
            <?php 
            endforeach; ?>
        </tbody>
    </table>
    </div>
<form method="POST" enctype="multipart/form-data" action="save.php" id="myForm">
    <input type="hidden" name="img_val" id="img_val" value="" />
</form>
<script>
     $(document).ready(function () {
         $("#previousMonth").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();               
             var date = moment($('#ActivitesreelleMonth').val()).subtract('M', 1);
             $('#ActivitesreelleMonth').val(date.format('YYYY-MM-DD'));
             $('#ActivitesreelleAbsencesForm').submit();
         });
         $("#nextMonth").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();                 
             var date = moment($('#ActivitesreelleMonth').val()).add('M', 1);
             $('#ActivitesreelleMonth').val(date.format('YYYY-MM-DD'));
             $('#ActivitesreelleAbsencesForm').submit();
         }); 
         $("#today").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();                
             var date = moment();
             $('#ActivitesreelleMonth').val(date.format('YYYY-MM-DD'));
             $('#ActivitesreelleAbsencesForm').submit();
         });  
         $("#all").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();                
             var date = moment();
             $('#ActivitesreellePass').val('0');
             $('#ActivitesreelleAbsencesForm').submit();
         });   
         $("#team").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();                
             var date = moment();
             $('#ActivitesreellePass').val('-1');
             $('#ActivitesreelleAbsencesForm').submit();
         });    
         
         $(".cercle").on('click', function(e){
             e.preventDefault();
             var overlay = $('#overlay');
             overlay.show();                
             var date = moment();
             var id = $(this).attr('data-id');
             $('#ActivitesreellePass').val(id);
             $('#ActivitesreelleAbsencesForm').submit();
         });         
         
        var downloadDataURI = function(options) {
          if(!options) {
            return;
          }
          $.isPlainObject(options) || (options = {data: options});
          if(!$.browser.webkit) {
            location.href = options.data;
            
          }
          options.filename || (options.filename = "download." + options.data.split(",")[0].split(";")[0].substring(5).split("/")[1]);
          options.url || (options.url = "http://download-data-uri.appspot.com/");
          $('<form method="post" action="'+options.url+'" style="display:none"><input type="hidden" name="filename" value="'+options.filename+'"/><input type="hidden" name="data" value="'+options.data+'"/></form>').submit().remove();
        }   
        
function downloadWithName(uri, name) {
    var link = document.createElement("a");
    link.download = name;
    link.href = uri;
    eventFire(link, "click");
}     

function eventFire(el, etype){
    if (el.fireEvent) {
        (el.fireEvent('on' + etype));
    } else {
        var evObj = document.createEvent('Events');
        evObj.initEvent(etype, true, false);
        el.dispatchEvent(evObj);
    }
}

         $("#canvas").on('click',function(e){
            $('#capture').html2canvas({
                    onrendered: function (canvas) {
                        var img = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                        var titre = 'Calendrier <?php echo $month.' '.$year; ?>.png';
                        if(!$.browser.webkit) {
                            //sauvegarde sous firefox impossible de changer le nom du fichier
                            Canvas2Image.saveAsPNG(canvas,false);
                        } else {
                            //sauvegarde sous Chrome avec nom du fichier forcé
                            downloadWithName(img, titre);
                        }
                        /*    downloadDataURI({
                              filename: titre,
                              data: img
                            });
                            */
                        //
                    }
                });             
         });
     });
</script>
