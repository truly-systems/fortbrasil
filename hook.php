<?php
/*
-------------------------------------------------------------------------
FortBrasil plugin for GLPI
Copyright (C) 2017 by the FortBrasil Development Team.

https://github.com/pluginsGLPI/fortbrasil
-------------------------------------------------------------------------

LICENSE

This file is part of FortBrasil.

FortBrasil is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

FortBrasil is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FortBrasil. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
*/

/**
* Plugin install process
*
* @return boolean
*/
function plugin_fortbrasil_install() {
  global $DB;

  if(!TableExists('glpi_plugin_fortbrasil_tickets')) {
    $query = "CREATE TABLE `glpi_plugin_fortbrasil_tickets` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `ticket_id` INT(11) NOT NULL,
      `id_conta` BIGINT(20),
      `nome` VARCHAR(45),
      `cpf` VARCHAR(15),
      `produto` VARCHAR(255),
      `telefone` VARCHAR(15),
      `email` VARCHAR(255),
      PRIMARY KEY(`id`)
    ) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";

    $DB->query($query) or die('error glpi_plugin_fortbrasil_tickets ' . $DB->error());
  }

  if(!TableExists('glpi_plugin_fortbrasil_templates')) {
    $query = "CREATE TABLE `glpi_plugin_fortbrasil_templates` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `template_id` INT(11) NOT NULL,
      PRIMARY KEY(`id`)
    ) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";

    $DB->query($query) or die('error glpi_plugin_fortbrasil_templates' . $DB->error());
  }

  CronTask::Register('PluginFortbrasilCron', 'ImportUsers', DAY_TIMESTAMP, array('state' => 0));

  return true;
}

/**
* Plugin uninstall process
*
* @return boolean
*/
function plugin_fortbrasil_uninstall() {
  global $DB;

  if(TableExists('glpi_plugin_fortbrasil_tickets')) {
    $query = "DROP TABLE `glpi_plugin_fortbrasil_tickets`";
    $DB->query($query) or die('error deleting glpi_plugin_fortbrasil_tickets');
  }
  
  if(TableExists('glpi_plugin_fortbrasil_templates')) {
    $query = "DROP TABLE `glpi_plugin_fortbrasil_templates`";
    $DB->query($query) or die('error deleting glpi_plugin_fortbrasil_templates');
  }

  return true;
}

// HOOKS
function pre_item_add_ticket(Ticket $item) {
  $operation = 'create';
  PluginFortbrasilTicket::prepareInput($item, $operation);
}

function item_add_ticket(Ticket $item) {
  $operation = 'create';
  PluginFortbrasilTicket::save($item, $operation);
}

function pre_item_update_ticket(Ticket $item) {
  $update = isset($item->input['id_conta_field']);

  if($update) {
    $operation = 'update';
    PluginFortbrasilTicket::prepareInput($item, $operation);
  }
}

function item_update_ticket(Ticket $item) {
  $update = isset($item->input['id_conta_field']);

  if($update) {
    $operation = 'update';
    PluginFortbrasilTicket::save($item, $operation);
  }
}

function pre_item_update_template(TicketTemplate $item) {
  PluginFortbrasilTemplate::save($item);
}
