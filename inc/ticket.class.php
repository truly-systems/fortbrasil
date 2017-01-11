<?php

class PluginFortBrasilTicket {

  static function beforeCreate(Ticket $item) {
    $item->input['name'] = $item->input['telefonefield'];

    $id_conta = $item->input['idcontafield'];
    $cpf      = $item->input['cpffield'];
    $nome     = $item->input['nomefield'];
    $content  = $item->input['content'];

    $description = "ID Conta:\t$id_conta\nCPF:\t$cpf\nNome:\t$nome\n\n$content";
    $item->input['content'] = $description;
  }

}

?>
