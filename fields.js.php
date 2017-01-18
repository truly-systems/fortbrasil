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
