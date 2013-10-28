<?php
ini_set('display_errors', 1);
set_time_limit(0);


require_once 'CRM/Core/Page.php';

class CRM_Gencontr_Page_GenRecurr extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle(ts('Generating contributions'));
        require_once '/home/maf/www/test/sites/civicrm_extensions/no.maf.recurring/Recurring/Form/Lookahead.php';
        
        $selectRecur = "SELECT * FROM civicrm_contribution_recur WHERE contribution_status_id <> 3";
        $daoRecur = CRM_Core_DAO::executeQuery($selectRecur);
        while ($daoRecur->fetch()) {
            $params['cid'] = $daoRecur->contact_id;
            $params['next_sched_contribution'] = $daoRecur->next_sched_contribution;
            $params['end_date'] = $daoRecur->end_date;
            $params['frequency_unit'] = $daoRecur->frequency_unit;
            $params['frequency_interval'] = $daoRecur->frequency_interval;
            $params['amount'] = $daoRecur->amount;
            $params['recur_id'] = $daoRecur->id;
            Recurring_Form_Lookahead::contributionCreate($params);
        }
        
        parent::run();
    }
}
