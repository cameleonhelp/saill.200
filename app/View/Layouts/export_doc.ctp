<?php
date_default_timezone_set("Europe/Paris");
$key = new DateTime();
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-word");
header ("Content-Disposition: attachment; filename=\"Rapport_".$this->params['controller'].'_'.$key->format("Y_m_d_H_i_s").".doc" );
header ("Content-Description: Generated Report" );
?>
<?php echo $content_for_layout ?> 