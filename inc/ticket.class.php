<?php

class PluginFortBrasilTicket {

  static function beforeCreate(Ticket $item) {
    $item->input['name'] = $item->input['telefone_field'];

    $id_conta = $item->input['id_conta_field'];
    $cpf      = $item->input['cpf_field'];
    $nome     = $item->input['nome_field'];
    $content  = $item->input['content'];

    $description = "ID Conta:\t$id_conta\nCPF:\t$cpf\nNome:\t$nome\n\n$content";
    $item->input['content'] = $description;
  }

  static function showCustomFields() {
    echo "<table class='tab_cadre_fixe'>";
    echo "<tbody>";

    // ID Conta
    echo "<tr class='tab_bg_1'>";
    echo "<th width='13%'>ID Conta</th>";
    echo "<td width='29%'><input type='text' name='id_conta_field' value=''></td>";
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
    echo "<td width='29%'><input type='text' name='cpf_field' value=''></td>";
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
    echo "<td width='29%'><input type='text' name='telefone_field' value=''></td>";
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

}

?>
