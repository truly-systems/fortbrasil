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

}

?>
