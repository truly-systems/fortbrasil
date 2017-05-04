<?php

include_once ('../../../inc/includes.php');

$id_conta = $_GET['id_conta'];

$user = new PluginFortbrasilUser();
$user->getFromDB($id_conta);

echo json_encode($user->fields);

?>
