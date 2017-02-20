<?php

$form = PluginFortBrasilTicket::showCustomFields();

//echo "<h1>Insert fields here...</h1>";
echo '<script>';
echo "$('#mainformtable').after('$form');";
echo '</script>';

?>
