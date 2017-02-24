<?php

$form = PluginFortbrasilTicket::showCustomFields();

echo '<script>';
echo "$('#mainformtable').after('$form');";
echo '</script>';

?>
