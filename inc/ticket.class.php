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

  static function showCustomFields() {
    $ticket_id  = self::getTicketID();

    $entity     = new Entity();
    $entity->getFromDB(0);

    $template_id  = $entity->getField('tickettemplates_id');
    $enabled      = PluginFortbrasilTemplate::isEnabled($template_id);

    if($enabled) {
      $fields     = new self();
      $fields     = $fields->find("ticket_id = $ticket_id");
      $fields     = ($fields) ? array_values($fields)[0] : null;

      $id_conta   = ($fields) ? $fields['id_conta'] : null;
      $nome       = ($fields) ? $fields['nome'] : null;
      $cpf        = ($fields) ? $fields['cpf'] : null;
      $produto    = ($fields) ? $fields['produto'] : null;
      $telefone   = ($fields) ? $fields['telefone'] : null;
      $email      = ($fields) ? $fields['email'] : null;

      $html = '';

      $html .= "<table class=\'tab_cadre_fixe\'>";
      $html .= "<tbody>";

      // ID Conta
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'13%\'>ID Conta</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'id_conta_field\' name=\'id_conta_field\' class=\'number\' value=\'$id_conta\' onchange=\'fill_fields()\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      // Nome
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'3%\'>Nome</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'nome_field\' name=\'nome_field\' value=\'$nome\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      // CPF
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'3%\'>CPF</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'cpf_field\' name=\'cpf_field\' class=\'cpf\' value=\'$cpf\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      // Produto
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'3%\'>Produto</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'produto_field\' name=\'produto_field\' value=\'$produto\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      // Telefone
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'3%\'>Telefone</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'telefone_field\' name=\'telefone_field\' class=\'telefone\' value=\'$telefone\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      // E-mail
      $html .= "<tr class=\'tab_bg_1\'>";
      $html .= "<th width=\'3%\'>E-mail</th>";
      $html .= "<td width=\'29%\'><input type=\'text\' id=\'email_field\' name=\'email_field\' value=\'$email\'></td>";
      $html .= "<td colspan=\'2\'></td>";
      $html .= "</tr>";

      $html .= "</tbody>";
      $html .= "</table>";

      echo $html;
    }

    echo $_SESSION['glpiactive_entity'];
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

  /*
  static function showForm($ID, $ticket) {
    $template_preview = '0';
    $type             = $ticket->fields['type'];
    $category         = $ticket->fields['itilcategories_id'];
    $entity           = $ticket->fields['entities_id'];

    $tt         = $ticket->getTicketTemplateToUse($template_preview, $type, $category, $entity);
    $is_enabled = PluginFortbrasilTemplate::isEnabled($tt->fields['id']);

    if($is_enabled) {
      $form = self::showCustomFields($ID);

      echo '<script>';
      echo "$('#mainformtable').after('$form');";
      echo '</script>';
    }
  }
  */

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
