<?php

$form = PluginFortBrasilTicket::showCustomFields();

echo '<script>';
echo "$('#mainformtable').after('$form');";
echo '</script>';

?>
