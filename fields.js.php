<?php

include ('../../inc/includes.php');

header('Content-type: application/javascript');

?>

$(window).load(function() {
  if(window.location.pathname.match(/ticket\.form\.php/)) {
    <!-- Máscara para telefone de 9 digítos -->
    var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions = {
      onKeyPress: function(val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      }
    };

    $('.cpf').mask('000.000.000-00');
    $('.telefone').mask(SPMaskBehavior, spOptions);
    $('.number').mask('#');

    var i = setInterval(function() {
      if($('#mainformtable').length) {
        clearInterval(i);
        inject_custom_fields();
      }
    }, 100);
  }

  function inject_custom_fields() {
    $('#mainformtable').after("<div id='fortbrasil-container'></div>");
    $('#fortbrasil-container').append("<?php PluginFortBrasilTicket::showCustomFields() ?>");
  }
});

function fill_fields() {
  var path      = '../plugins/fortbrasil/ajax/get_fields.php';
  var id_conta  = $('#id_conta_field').val();

  $.ajax({
    type: 'GET',
    dataType: 'json',
    url: path,
    data: 'id_conta=' + id_conta,

    success: function(data) {
      var nome      = (data['nome']) ? data['nome'] : '';
      var cpf       = (data['cpf']) ? data['cpf'] : '';
      var produto   = (data['produto']) ? data['produto'] : '';
      var telefone  = (data['telefone']) ? data['telefone'] : '';
      var email     = (data['email']) ? data['email'] : '';

      $('#nome_field').val(nome);
      $('#cpf_field').val(cpf);
      $('#produto_field').val(produto);
      $('#telefone_field').val(telefone);
      $('#email_field').val(email);
    }
  });
}
