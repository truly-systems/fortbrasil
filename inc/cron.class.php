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
    $root     = GLPI_ROOT;
    $filename = $task->fields['comment'];
    $filename = "$root/plugins/fortbrasil/files/$filename";
    $message  = '';

    if(is_file($filename)) {
      $data = self::getFileData($filename);

      $created_users = 0;
      $updated_users = 0;

      foreach($data as $row) {
        $user   = new User();
        $user_id = $user->getIdByName($row['ID_CONTA']);
        $email  = $row['EMAIL'];

        $user->fields['name']       = $row['ID_CONTA'];
        $user->fields['phone']      = $row['CPF'];
        $user->fields['firstname']  = $row['NOME'];
        $user->fields['realname']   = $row['PRODUTO'];
        $user->fields['mobile']     = '55' . $row['TELEFONE'];

        if(!$user_id) {
          self::insertUser($user, $email);
          $created_users++;
        } else {
          $user->fields['id'] = $user_id;
          self::updateUser($user, $email);
          $updated_users++;
        }
      }

      $message = "Inseridos: $created_users - Atualizados: $updated_users";
    } else {
      $message = "Não foi possível encontrar o arquivo $filename";
    }

    $task->log($message);
    return 1;
  }

  private static function getFileData($filename) {
    $row = 0;
    $col = 0;

    $handle = @fopen($filename, 'r');

    if($handle) {
      while(($row = fgetcsv($handle, 4096, ';')) !== false) {
        if(empty($fields)) {
          $fields = $row;
          continue;
        }

        foreach($row as $k=>$value) {
          $results[$col][$fields[$k]] = trim($value);
        }

        $col++;
        unset($row);
      }

      if(!feof($handle)) {
        echo 'Error: unexpected fgets() failn';
      }

      fclose($handle);
    }

    return $results;
  }

  private static function insertUser($user, $email) {
    $user_id = $user->addToDB();

    if($user_id) {
      self::setDefaultEmail($user_id, $email);
    }

    return $user_id;
  }

  private static function updateUser($user, $email) {
    global $DB;
    $table = 'glpi_users';

    $id_conta = $user->fields['name'];
    $cpf      = $user->fields['phone'];
    $nome     = $user->fields['firstname'];
    $produto  = $user->fields['realname'];
    $telefone = $user->fields['mobile'];

    $query = "UPDATE `$table`
            SET `phone` = '$cpf',
            `firstname` = '$nome',
            `realname` = '$produto',
            `mobile` = '$telefone'
            WHERE `name` = '$id_conta'";

    $DB->query($query);
    self::setDefaultEmail($user->fields['id'], $email);
  }

  private static function setDefaultEmail($user_id, $email) {
    $useremail    = new UserEmail();
    $email        = trim($email);
    $email_input  = array('email' => $email, 'users_id' => $user_id, 'is_default' => 0);

    $useremail->add($email_input);
  }
}

?>
