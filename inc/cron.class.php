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

    $files = explode(';', $task->fields['comment'], 2);

    $base_file  = "$plugin_root/files/" . $files[0];
    $new_file   = isset($files[1]) ? "$plugin_root/files/" . $files[1] : '';

    $cmd =  "bash $pdi_root/kitchen.sh -file=$plugin_root/lib/etl/job_import_users.kjb " .
            "-param:HOST=$host -param:DB=$db -param:PORT=$port -param:USER=$user " .
            "-param:PASSWORD=$password -param:BASE_FILE=$base_file -param:NEW_FILE=$new_file " .
            ">> $plugin_root/files/cron.log";

    exec($cmd, $out);

    $message = 'Usuários importados com sucesso.';

    $task->log($message);
    return 1;
  }
}

?>
