<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphTrOperationbooking\services;

class Element_OphTrOperationbooking_OperationService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'Element_OphTrOperationbooking_Operation';

	static protected $model_map = array(
		'Element_OphTrOperationbooking_Operation' => array(
			'reference_objects' => array(
				'eye' => array('eye_id', 'Eye', array('name')),
				'anaesthetic_type' => array('anaesthetic_type_id', 'AnaestheticType', array('name')),
				'priority' => array('priority_id', 'Priority', array('name')),
				'status' => array('status_id', 'OphTrOperationbooking_Operation_Status', array('name')),
				'cancellation_reason' => array('cancellation_reason_id', 'OphTrOperationbooking_Operation_Cancellation_Reason', array('name')),
			),
			'fields' => array(
				'event_ref' => array(self::TYPE_REF, 'event_id', 'Event'),
				'eye' => 'eye.name',
				'consultant_required' => 'consultant_required',
				'anaesthetic_type' => 'anaesthetic_type.name',
				'overnight_stay' => 'overnight_stay',
				'site_ref' => array(self::TYPE_REF, 'site_id', 'Site'),
				'priority' => 'priority.name',
				'decision_date' => 'decision_date',
				'comments' => 'comments',
				'total_duration' => 'total_duration',
				'status' => 'status.name',
				'anaesthetist_required' => 'anaesthetist_required',
				'operation_cancellation_date' => array(self::TYPE_SIMPLEOBJECT, 'operation_cancellation_date', 'Date'),
				'cancellation_user' => array(self::TYPE_REF, 'cancellation_user_id', 'User'),
				'cancellation_reason' => 'cancellation_reason.name',
				'cancellation_comment' => 'cancellation_comment',
				'latest_booking_ref' => array(self::TYPE_REF, 'latest_booking_id', 'OphTrOperationbooking_Operation_Booking'),
				'comments_rtt' => 'comments_rtt',
				'referral_ref' => array(self::TYPE_REF, 'referral_id', 'Referral'),
				'rtt_ref' => array(self::TYPE_REF, 'rtt_id', 'RTT'),
				'procedures' => array(self::TYPE_LIST, 'procedure_assignment', '\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Procedures', 'OphTrOperationbooking_Operation_Procedures', array('element_id' => 'primaryKey')),
				'bookings' => array(self::TYPE_LIST, 'allBookings', '\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Booking', 'OphTrOperationbooking_Operation_Booking', array('element_id' => 'primaryKey')),
			),
		),
		'\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Procedures' => array(
			'related_objects' => array(
				'element' => array('element_id', 'Element_OphTrOperationbooking_Operation'),
			),
			'reference_objects' => array(
				'procedure' => array('proc_id', 'Procedure', array('term')),
			),
			'fields' => array(
				'procedure' => 'procedure.term',
				'display_order' => 'display_order',
			),
		),
		'\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Booking' => array(
			'related_objects' => array(
				'element' => array('element_id', 'Element_OphTrOperationbooking_Operation'),
			),
			'reference_objects' => array(
				'cancellationReason' => array('cancellation_reason_id', 'OphTrOperationbooking_Operation_Cancellation_Reason', array('name')),
			),
			'fields' => array(
				'session_ref' => array(self::TYPE_REF, 'session_id', 'OphTrOperationbooking_Operation_Session'),
				'display_order' => 'display_order',
				'ward_ref' => array(self::TYPE_REF, 'ward_id', 'OphTrOperationbooking_Operation_Ward'),
				'admission_time' => 'admission_time',
				'confirmed' => 'confirmed',
				'session_date' => array(self::TYPE_SIMPLEOBJECT, 'session_date', 'Date'),
				'session_start_time' => 'session_start_time',
				'session_end_time' => 'session_end_time',
				'theatre_ref' => array(self::TYPE_REF, 'session_theatre_id', 'OphTrOperationbooking_Operation_Theatre'),
				'transport_arranged' => 'transport_arranged',
				'transport_arranged_date' => array(self::TYPE_SIMPLEOBJECT, 'transport_arranged_date', 'Date'),
				'booking_cancellation_date' => array(self::TYPE_SIMPLEOBJECT, 'booking_cancellation_date', 'DateTime'),
				'cancellation_reason' => 'cancellationReason.name',
				'cancellation_comment' => 'cancellation_comment',
				'cancellation_user' => array(self::TYPE_REF, 'cancellation_user_id', 'User'),
			),
		),
	);

	public function search(array $params)
	{
	}
}
