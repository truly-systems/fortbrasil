<?php

include_once ('../../../inc/includes.php');

$id_conta = $_GET['id_conta'];

echo json_encode(PluginFortbrasilTicket::findByIDConta($id_conta));

?>
