<?php

include_once('../../../inc/includes.php');

if(isset($_POST['template_id']) && isset($_POST['enable'])) {
  $template_id  = $_POST['template_id'];
  $enable       = $_POST['enable'];
  
  PluginFortbrasilTemplate::save($template_id, $enable);
}

Html::back();

?>
