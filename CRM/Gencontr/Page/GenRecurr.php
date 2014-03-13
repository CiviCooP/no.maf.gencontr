<?php
ini_set('display_errors', 1);
set_time_limit(0);


require_once 'CRM/Core/Page.php';

class CRM_Gencontr_Page_GenRecurr extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle(ts('Generating contributions'));
        require_once 'Recurring/Form/Lookahead.php';
       
        $selectRecur = 
"SELECT * FROM civicrm_contribution_recur WHERE contribution_status_id <> 3";
        $daoRecur = CRM_Core_DAO::executeQuery($selectRecur);
        while ($daoRecur->fetch()) {
            
            $nextSchedContribution = $daoRecur->next_sched_contribution;
			$date    = new DateTime($nextSchedContribution);
			if ($date->format('n') == 10) {
				/*if ($daoRecur->frequence_interval == 1 && $daoRecur->frequency_unit == "month") {
					$date    = new DateTime($nextSchedContribution);*/
					$date->modify(
						'+' . $daoRecur->frequency_interval .
						' ' . $daoRecur->frequency_unit
					);
					$nextSchedContribution = $date->format('c');
				/* } */
            }
            
            $params['cid'] = $daoRecur->contact_id;
            $params['next_sched_contribution'] = $nextSchedContribution;
            $params['end_date'] = $daoRecur->end_date;
            $params['frequency_unit'] = $daoRecur->frequency_unit;
            $params['frequency_interval'] = $daoRecur->frequency_interval;
			$params['financial_type_id'] = $daoRecur->financial_type_id;
            $params['amount'] = $daoRecur->amount;
            $params['recur_id'] = $daoRecur->id;
            $this->contributionCreate($params);
        }
        
        parent::run();
    }
	
	protected function contributionCreate($params) {

        // loop to create all scheduled contributions between 
        // next_sched_contribution and recur end_date or 45 days time

        $status_id = array_flip(CRM_Contribute_PseudoConstant::contributionStatus());
        $error     = false;
        $date      = null;

        for (
            
            // initializer
            $date    = new DateTime($params['next_sched_contribution']),
            $counter = 0; 
            
            // condition
            (!empty($params['end_date']) ? $date < new DateTime($params['end_date']) : true) and 
            ($date < new DateTime('now +' . MAF_RECURRING_DAYS_LOOKAHEAD . ' day')); 
            
            // incrementer
            $date->modify(
                '+' . $params['frequency_interval'] .
                ' ' . $params['frequency_unit']
            ),
            $counter++

        ) {

            try {
                civicrm_api3('contribution', 'create', array(
                    'total_amount'           => $params['amount'],
                    'financial_type_id'      => $params['financial_type_id'], 
                    'contact_id'             => $params['cid'],
                    'receive_date'           => $date->format('c'),
                    'trxn_id'                => '',
                    'invoice_id'             => md5(uniqid(rand())),
                    'source'                 => ts('Offline Recurring Contribution'),
                    'contribution_status_id' => $status_id['Pending'],
                    'contribution_recur_id'  => $params['recur_id'] 
                ));
            } catch (CiviCRM_API3_Exception $e) {
                $error = $e->getMessage();
                break;
            }

        }

        if ($error) {
            
            CRM_Core_Error::fatal(ts(
                'An error occurred creating initial contributions for ' . 
                'contribution_recur_id %1 in %2::%3: %4',
                array(
                    1 => $params['recur_id'],
                    2 => __CLASS__,
                    3 => __METHOD__,
                    4 => $error
                )
            ));

        } else {
            
            // Update next_sched_date on civicrm_contribution_recur
            CRM_Core_DAO::executeQuery("
                UPDATE civicrm_contribution_recur 
                   SET next_sched_contribution = %1 
                 WHERE id = %2
            ", array(
                   1 => array($date->format('c'), 'String'),
                   2 => array($params['recur_id'], 'Positive')
               )
            );

            ocr_set_message(ts(
                'Created %1 contribution(s) up until %2 days time, or the end date you specified (if sooner).',
                array(
                    1 => $counter,
                    2 => MAF_RECURRING_DAYS_LOOKAHEAD
                )
            ));

        }
    
    }
}
