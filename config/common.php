<?php
/**
 * OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

return array(
	'import' => array(
		'application.modules.OphTrOperationbooking.components.OphTrOperationbookingObserver',
	),
	'components' => array(
		'event' => array(
			'observers' => array(
				'firm_changed' => array(
					'ophtroperationbooking_resetsearch' => array(
						'class' => 'OphTrOperationbookingObserver',
						'method' => 'resetSearch',
					),
				),
			),
		),
		'service' => array(
			'class' => 'services\ServiceManager',
			'internal_services' => array(
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Letter_Contact_RuleService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Cancellation_ReasonService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_EROD_RuleService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService',
				'OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService',
			),
		),
	),
	'params' => array(
		'menu_bar_items' => array(
			'theatre_diaries' => array(
				'title' => 'Theatre Diaries',
				'uri' => 'OphTrOperationbooking/theatreDiary/index',
				'position' => 10,
			),
			'partial_bookings' => array(
				'title' => 'Partial bookings waiting list',
				'uri' => 'OphTrOperationbooking/waitingList/index',
				'position' => 20,
			),
		),
		'admin_menu' => array(
			'Sequences' => '/OphTrOperationbooking/admin/viewSequences',
			'Sessions' => '/OphTrOperationbooking/admin/viewSessions',
			'Wards' => '/OphTrOperationbooking/admin/viewWards',
			'Theatres' => '/OphTrOperationbooking/admin/viewTheatres',
			'Scheduling options' => '/OphTrOperationbooking/admin/viewSchedulingOptions',
			'EROD rules' => '/OphTrOperationbooking/admin/viewERODRules',
			'Letter contact rules' => '/OphTrOperationbooking/admin/viewLetterContactRules',
			'Letter warning rules' => '/OphTrOperationbooking/admin/viewLetterWarningRules',
			'Operation name rules' => '/OphTrOperationbooking/admin/viewOperationNameRules',
			'Waiting list contact rules' => '/OphTrOperationbooking/admin/viewWaitingListContactRules',
			'Patient unavailable reasons' => '/OphTrOperationbooking/admin/viewPatientUnavailableReasons',
			'Session unavailable reasons' => '/OphTrOperationbooking/admin/viewSessionUnavailableReasons'
		),
		// Default anaesthetic settings
		//'ophtroperationbooking_default_anaesthetic_child' => 'GA',
		//'ophtroperationbooking_default_anaesthetic' => 'GA',
		// How many weeks from DTA should EROD be calculated
		//'erod_lead_time_weeks' => 3,
		// How many days ahead of the day an operation is being scheduled should EROD be calculated
		//'erod_lead_current_date_days' => 2,
		// number of weeks from decision date that is the RTT limit
		//'ophtroperationboooking_rtt_limit' => 6,
		// whether referrals can be assigned to operation bookings or not (turn off if you don't have referrals imported
		// or set on the patient record.
		//'ophtroperationbooking_referral_link' => true,
		// boolean to require a referral on an operation booking for scheduling or not
		//'ophtroperationbooking_schedulerequiresreferral' => true
	)
);
