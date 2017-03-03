<?php

class PluginFortbrasilTemplate extends CommonITILObject {
  
  function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
    return __('FortBrasil - Campos', 'fortbrasil');
  }
  
  static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
    global $CFG_GLPI;

    $action = $CFG_GLPI['root_doc'] . '/plugins/fortbrasil/front/template.form.php';
    
    $template_id  = $item->fields['id'];
    $enabled      = self::isEnabled($template_id);

    echo "<form name='form' action='$action' method='post'>";
    echo "<div class='center' id='tabsbody'>";
    echo "<table class='tab_cadre_fixe'>";
    echo "<tr><th colspan='4'>" . __('FortBrasil - Campos Adicionais') . "</th></tr>";
    echo "<td >" . __('Incluir campos:') . "</td>";
    echo "<td colspan='3'>";
    echo "<input type='hidden' name='template_id' value='$template_id'>";
    Dropdown::showYesNo('enable', $enabled);
    echo "</td></tr>";

    echo "<tr class='tab_bg_2'>";
    echo "<td colspan='4' class='center'>";
    echo "<input type='submit' name='update' class='submit' value=\""._sx('button','Save')."\">";
    echo "</td></tr>";

    echo "</table></div>";
    Html::closeForm();
  }
  
  static function save($template_id, $enable) {
    if($enable) {
      self::enable($template_id);
    } else {
      self::disable($template_id);
    }
  }

  static function isEnabled($template_id) {
    global $DB;
    $table = self::getTable();

    $query = "SELECT IF(COUNT(*) > 0, TRUE, FALSE) AS `enabled`
            FROM `glpi_plugin_fortbrasil_templates`
            WHERE `template_id` = '$template_id'";

    $result = $DB->query($query);
    return $DB->result($result, 0, 'enabled');
  }

  private static function enable($template_id) {
    $enabled = self::isEnabled($template_id);

    if(!$enabled) {
      global $DB;
      $table = self::getTable();

      $query = "INSERT INTO $table (`template_id`) VALUES ('$template_id')";
      $DB->query($query);
    }
  }

  private static function disable($template_id) {
    global $DB;
    $table = self::getTable();

    $query = "DELETE FROM $table WHERE `template_id` = '$template_id'";
    $DB->query($query);
  }
}

?>