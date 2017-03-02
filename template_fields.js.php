<?php
  include ('../../inc/includes.php');
  header('Content-type: application/javascript');
?>

$(window).load(function() {
  if(window.location.pathname.match(/tickettemplate\.form\.php/)) {
    var i = setInterval(function() {
      if($('.tab_bg_1').length) {
        clearInterval(i);
        inject_checkbox();
      }
    }, 250);
  }
  
  function inject_checkbox() {
    $('.tab_bg_1:first').after("<?php PluginFortbrasilTemplate::showCheckbox() ?>");
  }
});
