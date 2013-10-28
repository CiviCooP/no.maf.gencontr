<?php

require_once 'gencontr.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function gencontr_civicrm_config(&$config) {
  _gencontr_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function gencontr_civicrm_xmlMenu(&$files) {
  _gencontr_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function gencontr_civicrm_install() {
    /*
     * check if recurring extension has been installed
     */
    if (!defined('MAF_RECURRING_DAYS_LOOKAHEAD')) {
        CRM_Core_Error::fatal(ts(
            "Unable to generate pending contributions as the MAF recurring contributions extension is not installed/enabled"
        )); 
    }
  return _gencontr_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function gencontr_civicrm_uninstall() {
  return _gencontr_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function gencontr_civicrm_enable() {
  return _gencontr_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function gencontr_civicrm_disable() {
  return _gencontr_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function gencontr_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _gencontr_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function gencontr_civicrm_managed(&$entities) {
  return _gencontr_civix_civicrm_managed($entities);
}
