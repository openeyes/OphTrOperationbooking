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
	'session1' => array(
		'id' => 1,
		'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
		'firm_id' => 1,
		'date' => date('Y-m-d', strtotime('-2 weeks')),
		'start_time' => '10:00',
		'end_time' => '12:00',
		'available' => 1,
		'consultant' => 0,
		'paediatric' => 0,
		'anaesthetist' => 0,
		'general_anaesthetic' => 0,
		'unavailable_reason_id' => null,
		'max_procedures' => null,
		'comments' => 'i am a comment',
		'theatre_id' => 2,
		'default_admission_time' => '11:00:00',
	),
	'session2' => array(
		'id' => 2,
		'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
		'firm_id' => 1,
		'date' => date('Y-m-d', strtotime('+3 days')),
		'start_time' => '10:00',
		'end_time' => '12:00',
		'available' => true,
		'consultant' => false,
		'paediatric' => false,
		'anaesthetist' => false,
		'general_anaesthetic' => false,
		'unavailable_reason_id' => null,
		'max_procedures' => null,
	),
	'session3' => array(
		'id' => 3,
		'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
		'firm_id' => 1,
		'date' => date('Y-m-d', strtotime('+3 weeks')),
		'start_time' => '10:00',
		'end_time' => '12:00',
		'available' => true,
		'consultant' => false,
		'paediatric' => false,
		'anaesthetist' => false,
		'general_anaesthetic' => false,
		'unavailable_reason_id' => null,
		'max_procedures' => null,
	),
		'session4' => array(
				'id' => 4,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+5 weeks')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => true,
				'consultant' => false,
				'paediatric' => false,
				'anaesthetist' => false,
				'general_anaesthetic' => false,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'session5' => array(
				'id' => 5,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+3 weeks')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => 1,
				'consultant' => 1,
				'paediatric' => 0,
				'anaesthetist' => 0,
				'general_anaesthetic' => 0,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
				'theatre_id' => 2,
		),
		'session6' => array(
				'id' => 6,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+3 weeks')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => true,
				'consultant' => false,
				'paediatric' => true,
				'anaesthetist' => false,
				'general_anaesthetic' => false,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'session7' => array(
				'id' => 7,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+3 weeks')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => true,
				'consultant' => false,
				'paediatric' => false,
				'anaesthetist' => true,
				'general_anaesthetic' => true,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'session8' => array(
				'id' => 8,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+3 weeks + 2 days')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => true,
				'consultant' => false,
				'paediatric' => true,
				'anaesthetist' => false,
				'general_anaesthetic' => false,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'unavailable1' => array(
				'id' => 9,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 1,
				'date' => date('Y-m-d', strtotime('+2 weeks +6 days')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => false,
				'consultant' => false,
				'paediatric' => false,
				'anaesthetist' => false,
				'general_anaesthetic' => false,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'session10' => array(
				'id' => 10,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 2,
				'date' => date('Y-m-d', strtotime('+2 weeks +6 days')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => true,
				'consultant' => false,
				'paediatric' => false,
				'anaesthetist' => false,
				'general_anaesthetic' => false,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
		'session11' => array(
				'id' => 11,
				'sequence_id' => 1, // am not worrying about consistency with sequences at this point.
				'firm_id' => 2,
				'date' => date('Y-m-d', strtotime('+2 weeks +6 days')),
				'start_time' => '10:00',
				'end_time' => '12:00',
				'available' => 1,
				'consultant' => 1,
				'paediatric' => 1,
				'anaesthetist' => 1,
				'general_anaesthetic' => 1,
				'unavailable_reason_id' => null,
				'max_procedures' => null,
		),
);
