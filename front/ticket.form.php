<?php
  include ('../../../inc/includes.php');

  if(isset($_GET['category_id']) && isset($_GET['type'])) {
    $category_id  = $_GET['category_id'];
    $type         = $_GET['type'];

    PluginFortBrasilTicket::showCustomFields($category_id, $type);
  }
?>
