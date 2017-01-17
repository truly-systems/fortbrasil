<?php

include ('../../inc/includes.php');

header('Content-type: application/javascript');

?>

$(window).load(function() {
  if(window.location.pathname.match(/ticket\.form\.php/)) {
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
