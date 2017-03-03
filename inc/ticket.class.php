<?php

class PluginFortbrasilTicket extends CommonITILObject {

  static function prepareInput(Ticket $item, $operation) {
    // Get input values
    $id_conta   = $item->input['id_conta_field'];
    $nome       = $item->input['nome_field'];
    $cpf        = $item->input['cpf_field'];
    $produto    = $item->input['produto_field'];
    $telefone   = $item->input['telefone_field'];
    $email      = $item->input['email_field'];
    $content    = $item->input['content'];

    $ddi = '55';

    // Remove non digit characters
    $chars    = array('(', ')', '-', ' ');
    $telefone = str_replace($chars, '', $telefone);

    $item->input['name']    = "$ddi$telefone";
    $item->input['content'] = self::prepareContentInput($content, $id_conta, $cpf, $nome);

    if($operation == 'create') {
      $item->input['_users_id_observer_notif']['use_notification']  = array(1);
      $item->input['_users_id_observer_notif']['alternative_email'] = array($email);
    } else if($operation == 'update') {
      $ticket_id = $item->fields['id'];
      $ticket_user = new Ticket_User();
      $ticket_user->getFromDBByQuery("WHERE `tickets_id` = '$ticket_id' AND `type` = '3'");
      $ticket_user->fields['alternative_email'] = $email;
      $ticket_user->updateInDB(array('alternative_email'));
    }
  }

  static function save(Ticket $item, $operation) {
    // Get input values
    $ticket_id  = $item->fields['id'];
    $id_conta   = $item->input['id_conta_field'];
    $nome       = $item->input['nome_field'];
    $cpf        = $item->input['cpf_field'];
    $produto    = $item->input['produto_field'];
    $telefone   = $item->input['telefone_field'];
    $email      = $item->input['email_field'];

    if($operation == 'create') {
      self::createTicket($ticket_id, $id_conta, $nome, $cpf, $produto, $telefone, $email);
    } else if($operation == 'update') {
      self::updateTicket($ticket_id, $id_conta, $nome, $cpf, $produto, $telefone, $email);
    }
  }

  static function showCustomFields($category_id, $type) {
    $active_entity = $_SESSION['glpiactive_entity'];
    $entity        = new Entity();

    $entity->getFromDB($active_entity);

    $template_id  = $entity->getField('tickettemplates_id');
    $enabled      = PluginFortbrasilTemplate::isEnabled($template_id, $category_id, $type);

    if($enabled) {
      $ticket_id  = self::getTicketID();

      $fields     = new self();
      $fields     = $fields->find("ticket_id = $ticket_id");
      $fields     = ($fields) ? array_values($fields)[0] : null;

      $id_conta   = ($fields) ? $fields['id_conta'] : null;
      $nome       = ($fields) ? $fields['nome'] : null;
      $cpf        = ($fields) ? $fields['cpf'] : null;
      $produto    = ($fields) ? $fields['produto'] : null;
      $telefone   = ($fields) ? $fields['telefone'] : null;
      $email      = ($fields) ? $fields['email'] : null;

      echo "<table class='tab_cadre_fixe'>";
      echo "<tbody>";

      // ID Conta
      echo "<tr class='tab_bg_1'>";
      echo "<th width='13%'>ID Conta</th>";
      echo "<td width='29%'><input type='text' id='id_conta_field' name='id_conta_field' class='number' value='$id_conta' onchange='fill_fields()'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      // Nome
      echo "<tr class='tab_bg_1'>";
      echo "<th width='3%'>Nome</th>";
      echo "<td width='29%'><input type='text' id='nome_field' name='nome_field' value='$nome'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      // CPF
      echo "<tr class='tab_bg_1'>";
      echo "<th width='3%'>CPF</th>";
      echo "<td width='29%'><input type='text' id='cpf_field' name='cpf_field' class='cpf' value='$cpf'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      // Produto
      echo "<tr class='tab_bg_1'>";
      echo "<th width='3%'>Produto</th>";
      echo "<td width='29%'><input type='text' id='produto_field' name='produto_field' value='$produto'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      // Telefone
      echo "<tr class='tab_bg_1'>";
      echo "<th width='3%'>Telefone</th>";
      echo "<td width='29%'><input type='text' id='telefone_field' name='telefone_field' class='telefone' value='$telefone'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      // E-mail
      echo "<tr class='tab_bg_1'>";
      echo "<th width='3%'>E-mail</th>";
      echo "<td width='29%'><input type='text' id='email_field' name='email_field' value='$email'></td>";
      echo "<td colspan='2'></td>";
      echo "</tr>";

      echo "</tbody>";
      echo "</table>";
    }
  }

  static function findByIDConta($id_conta) {
    $user = new User();
    $user->getFromDBbyName($id_conta);

    $fields = array();

    if(isset($user->fields['name'])) {
      $fields = array(
        'nome'      => $user->fields['firstname'],
        'cpf'       => $user->fields['phone'],
        'produto'   => $user->fields['realname'],
        'telefone'  => substr($user->fields['mobile'], 2),
        'email'     => $user->getDefaultEmail()
      );
    }

    return $fields;
  }

  // Obtém o Ticket de acordo com o ID passado na URL
  private static function getTicketID() {
    $ticket = null;

    $url = $_SERVER['HTTP_REFERER'];
    $parts = parse_url($url);

    if(isset($parts['query'])) {
      parse_str($parts['query'], $query);
      $ticket = isset($query['id']) ? $query['id'] : null;
    }

    return $ticket;
  }

  private static function prepareContentInput($content, $id_conta, $cpf, $nome) {
    // Clear field values from content
    $fields = array('ID Conta', 'CPF', 'Nome');

    foreach($fields as $field) {
      $pattern = "/($field.*?)(\\\\r|\\\\n|$)/";
      $content = preg_replace($pattern, '', $content);
    }

    // Trim new line characters
    $pattern = array('/^(\\\\n|\\\\r)+/', '/(\\\\n|\\\\r)+$/');
    $content = preg_replace($pattern, '', $content);

    $content = "ID Conta:\t$id_conta\nCPF:\t$cpf\nNome:\t$nome\n\n$content";

    return $content;
  }

  private static function createTicket($ticket_id, $id_conta, $nome, $cpf, $produto, $telefone, $email) {
    global $DB;
    $table = self::getTable();

    $query = "INSERT INTO $table (`ticket_id`, `id_conta`, `nome`, `cpf`, `produto`, `telefone`, `email`)
              VALUES('$ticket_id', '$id_conta', '$nome', '$cpf', '$produto', '$telefone', '$email')";

    $DB->query($query);
  }

  private static function updateTicket($ticket_id, $id_conta, $nome, $cpf, $produto, $telefone, $email) {
    global $DB;
    $table = self::getTable();

    $query = "UPDATE `glpi_plugin_fortbrasil_tickets`
              SET `id_conta` = '$id_conta', `nome` = '$nome', `cpf` = '$cpf', `produto` = '$produto',
              `telefone` = '$telefone', `email` = '$email'  WHERE `ticket_id` = '$ticket_id'";

    $DB->query($query);
  }
}

?>
