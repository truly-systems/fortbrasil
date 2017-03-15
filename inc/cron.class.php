<?php

class PluginFortbrasilCron {

  static function getTypeName($nb = 0) {
    return 'FortBrasil';
  }

  static function cronInfo($name) {
    switch($name) {
      case 'ImportUsers':
        return array('description' => __('Import users from CSV file', 'fortbrasil'));
    }

    return array();
  }

  static function cronImportUsers($task) {
    $root         = GLPI_ROOT;
    $plugin_root  = "$root/plugins/fortbrasil";
    $pdi_root     = "$plugin_root/lib/data-integration";

    $conn     = DBConnection::getReadConnection();
    $host     = $conn->dbhost;
    $user     = $conn->dbuser;
    $password = $conn->dbpassword;
    $db       = $conn->dbdefault;
    $port     = '3306';

    $file = $task->fields['comment'];
    $file = "$plugin_root/files/$file";

    $cmd = "bash $pdi_root/pan.sh -file=$plugin_root/lib/import_users.ktr -param:HOST=$host -param:DB=$db -param:PORT=$port -param:USER=$user -param:PASSWORD=$password -param:FILE=$file";
    exec($cmd, $out);

    $message = 'Usuários importados com sucesso.';

    $task->log($message);
    return 1;
  }
}

?>
