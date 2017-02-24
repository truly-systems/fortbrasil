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

  static function showCheckbox() {
    $template_id = self::getTemplateID();

    $enabled = self::isEnabled($template_id);
    $checked = $enabled ? 'checked' : '';

    $html = '<tr>';
    $html .= '<td>Incluir</td>';
    $html .= "<td><input type='checkbox' name='active' " . $checked . "></td>";
    $html .= '</tr>';

    echo $html;
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

  // Obtém o Template de acordo com o ID passado na URL
  private static function getTemplateID() {
    $template = null;

    $url = $_SERVER['HTTP_REFERER'];
    $parts = parse_url($url);

    if(isset($parts['query'])) {
      parse_str($parts['query'], $query);
      $template = isset($query['id']) ? $query['id'] : null;
    }

    return $template;
  }
}

?>
