<?php

require_once 'CRM/Core/Page.php';

class CRM_Gencontr_Page_GenContr extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Generate pending contributions'));
    $this->assign('processUrl', CRM_Utils_System::url('civicrm/genrecurr', null, true));
    parent::run();
  }
}
