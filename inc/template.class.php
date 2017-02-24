<?php

class PluginFortbrasilTemplate extends CommonITILObject {
  static function save(TicketTemplate $item) {
    $template_id = $item->input['id'];
    $active      = isset($item->input['active']);

    if($active) {
      self::enable($template_id);
    } else {
      self::disable($template_id);
    }
  }

  static function showCheckbox($enabled) {
    $checked = $enabled ? 'checked' : '';

    $html = '<tr>';
    $html .= '<td>Incluir</td>';
    $html .= '<td><input type="checkbox" name="active" ' . $checked .'></td>';
    $html .= '</tr>';

    return $html;
  }

  static function showForm($ID, $template) {
    $enabled  = self::isEnabled($ID);
    $checkbox = self::showCheckbox($enabled);

    echo '<script>';
    echo "$('.footerRow').before('$checkbox');";
    echo '</script>';
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
