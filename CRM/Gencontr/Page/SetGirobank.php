<?php
ini_set('display_errors', 1);
require_once 'CRM/Core/Page.php';

class CRM_Gencontr_Page_SetGirobank extends CRM_Core_Page {
  function run() {
        
        $customGroupParams = array(
            'version'   =>  3,
            'title'     =>  "Nets Transactions"
        );
        $customGroup = civicrm_api('CustomGroup', 'Getsingle', $customGroupParams);
        if (!civicrm_error($customGroup)) {
            if (isset($customGroup['table_name'])) {
                $customGroupTable = $customGroup['table_name'];
            }
            if (isset($customGroup['id'])) {
                $customGroupId = $customGroup['id'];
            }
        }
        $customFieldParams = array(
            'version'           =>  3,
            'custom_group_id'   =>  $customGroupId,
            'label'             =>  "sent to bank"
        );
        $customField = civicrm_api('CustomField', 'Getsingle', $customFieldParams);
        if (!civicrm_error($customField)) {
            if (isset($customField['column_name'])) {
                $customFieldColumn = $customField['column_name'];
            }
        }
                
        $selectContribution = 
"SELECT 
contr.id AS contribution_id
FROM `civicrm_contribution_recur_offline` off
LEFT JOIN civicrm_contribution_recur recur ON off.recur_id = recur.id
LEFT JOIN civicrm_contribution contr ON off.recur_id = contr.contribution_recur_id
WHERE off.payment_type_id = 3 AND recur.contribution_status_id = 2 and contr.id > 700000";
        $daoContribution = CRM_Core_DAO::executeQuery($selectContribution);
        while ($daoContribution->fetch()) {
            $insNets = "INSERT INTO $customGroupTable SET entity_id = $daoContribution->contribution_id, ";
            $insNets .= $customFieldColumn." = 1";
            $selNets = "SELECT COUNT(*) AS countNets FROM $customGroupTable WHERE entity_id = $daoContribution->contribution_id";
            $daoNets = CRM_Core_DAO::executeQuery($selNets);
            if ($daoNets->fetch()) {
                if (isset($daoNets->countNets) && $daoNets->countNets == 0) {
                    CRM_Core_DAO::executeQuery($insNets);
                }
            }
        }
        parent::run();
  }
}
