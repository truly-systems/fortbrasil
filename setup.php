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

include_once('inc/ticket.class.php');

define('PLUGIN_FORTBRASIL_VERSION', '1.0.0');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_fortbrasil() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['fortbrasil']   = true;

   $PLUGIN_HOOKS['pre_item_add']['fortbrasil']     = array('Ticket' => 'pre_item_add_ticket');
   $PLUGIN_HOOKS['item_add']['fortbrasil']         = array('Ticket' => 'item_add_ticket');
   $PLUGIN_HOOKS['pre_item_update']['fortbrasil']  = array('Ticket' => 'pre_item_update_ticket');
   $PLUGIN_HOOKS['item_update']['fortbrasil']      = array('Ticket' => 'item_update_ticket');

   $PLUGIN_HOOKS['post_show_item']['fortbrasil']   = 'post_show_ticket';

   $PLUGIN_HOOKS['add_javascript']['fortbrasil'][] = 'fields.js.php';
   $PLUGIN_HOOKS['add_javascript']['fortbrasil'][] = 'jquery.mask.js';
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_fortbrasil() {
   return [
      'name'           => 'FortBrasil',
      'version'        => PLUGIN_FORTBRASIL_VERSION,
      'author'         => '<a href="http://trulymanager.com">Truly Systems</a>',
      'license'        => '',
      'homepage'       => '',
      'minGlpiVersion' => '0.90.0'
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_fortbrasil_check_prerequisites() {
   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION,'0.90.0','lt')) {
      echo "This plugin requires GLPI >= 0.90.0";
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_fortbrasil_check_config($verbose=false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'fortbrasil');
   }
   return false;
}
