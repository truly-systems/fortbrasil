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
    $filename = $task->fields['comment'];
    $data     = self::getFileData($filename);

    foreach($data as $row) {
      $id_conta = $row['ID_CONTA'];
      $cpf      = $row['CPF'];
      $nome     = $row['NOME'];
      $produto  = $row['PRODUTO'];
      $ddd      = $row['DDD'];
      $telefone = $row['TELEFONE'];
      $email    = $row['EMAIL'];

      $telefone = "55$ddd$telefone";

      self::insertUser($id_conta, $cpf, $nome, $produto, $telefone, $email);
    }

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

  static function insertUser($id_conta, $cpf, $nome, $produto, $telefone, $email) {
    $user = new User();

    $user->fields['name']       = $id_conta;
    $user->fields['phone']      = $cpf;
    $user->fields['firstname']  = $nome;
    $user->fields['realname']   = $produto;
    $user->fields['mobile']     = $telefone;

    $user_id = $user->addToDB();

    if($user_id) {
      self::setDefaultEmail($user_id, $email);
    }

    return $user_id;
  }

  static function setDefaultEmail($user_id, $email) {
    $useremail    = new UserEmail();
    $email        = trim($email);
    $email_input  = array('email' => $email, 'users_id' => $user_id, 'is_default' => 0);

    $useremail->add($email_input);
  }
}

?>
