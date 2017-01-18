<?php

class PluginFortBrasilTicket extends CommonITILObject {

  static function beforeCreate(Ticket $item) {
    // Get input values
    $id_conta     = $item->input['id_conta_field'];
    $nome         = $item->input['nome_field'];
    $cpf          = $item->input['cpf_field'];
    $produto      = $item->input['produto_field'];
    $telefone     = $item->input['telefone_field'];
    $ddi          = $item->input['ddi_field'];
    $content      = $item->input['content'];
    $watcher      = $item->input['_users_id_observer'];

    // Remove mask characters
    $chars    = array('(', ')', '-', ' ');
    $telefone = str_replace($chars, '', $telefone);

    $description  = "ID Conta:\t$id_conta\nCPF:\t$cpf\nNome:\t$nome\n\n$content";

    $item->input['name']    = "$ddi$telefone";
    $item->input['content'] = $description;

    $watcher_id = User::getIdByName($id_conta);
    array_push($watcher, $watcher_id);
  }

  static function showCustomFields() {
    echo "<table class='tab_cadre_fixe'>";
    echo "<tbody>";

    // ID Conta
    echo "<tr class='tab_bg_1'>";
    echo "<th width='13%'>ID Conta</th>";
    echo "<td width='29%'><input type='text' name='id_conta_field' class='number'></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    // Nome
    echo "<tr class='tab_bg_1'>";
    echo "<th width='3%'>Nome</th>";
    echo "<td width='29%'><input type='text' name='nome_field' value=''></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    // CPF
    echo "<tr class='tab_bg_1'>";
    echo "<th width='3%'>CPF</th>";
    echo "<td width='29%'><input type='text' name='cpf_field' class='cpf'></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    // Produto
    echo "<tr class='tab_bg_1'>";
    echo "<th width='3%'>Produto</th>";
    echo "<td width='29%'><input type='text' name='produto_field' value=''></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    // Telefone
    echo "<tr class='tab_bg_1'>";
    echo "<th width='3%'>Telefone</th>";
    echo "<td width='29%'>" .
    "<input type='text' name='ddi_field' class='number' size='3' value='55'>" .
    "<input type='text' name='telefone_field' class='telefone' size='14' style='margin-left: 2px'>" .
    "</td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    // E-mail
    echo "<tr class='tab_bg_1'>";
    echo "<th width='3%'>E-mail</th>";
    echo "<td width='29%'><input type='text' name='email_field' value=''></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";

    echo "</tbody>";
    echo "</table>";
  }

  // Obtém o Ticket de acordo com o ID passado na URL
  private static function getTicket() {
    $ticket = null;

    $url = $_SERVER['HTTP_REFERER'];
    $parts = parse_url($url);

    if(isset($parts['query'])) {
      parse_str($parts['query'], $query);
      $id = $query['id'];

      $ticket = new Ticket();
      $ticket->getFromDB($id);
    }

    return $ticket;
  }
}

?>
