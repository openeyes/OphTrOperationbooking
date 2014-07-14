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

class OphTrOperationbooking_Operation_SessionService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'OphTrOperationbooking_Operation_Session';

	static public $model_map = array(
		'OphTrOperationbooking_Operation_Session' => array(
			'reference_objects' => array(
				'unavailablereason' => array('unavailablereason_id', 'OphTrOperationbooking_Operation_Session_UnavailableReason', array('name')),
			),
			'fields' => array(
				'sequence_ref' => array(self::TYPE_REF, 'sequence_id', 'OphTrOperationbooking_Operation_Sequence'),
				'firm_ref' => array(self::TYPE_REF, 'firm_id', 'Firm'),
				'date' => array(self::TYPE_SIMPLEOBJECT, 'date', 'Date'),
				'start_time' => 'start_time',
				'end_time' => 'end_time',
				'comments' => 'comments',
				'available' => 'available',
				'consultant' => 'consultant',
				'paediatric' => 'paediatric',
				'anaesthetist' => 'anaesthetist',
				'general_anaesthetic' => 'general_anaesthetic',
				'theatre_ref' => array(self::TYPE_REF, 'theatre_id', 'OphTrOperationbooking_Operation_Theatre'),
				'default_admission_time' => 'default_admission_time',
				'unavailable_reason' => 'unavailablereason.name',
				'max_procedures' => 'max_procedures',
				'bookings' => array(self::TYPE_REF_LIST, 'activeBookingEvents', 'id', 'Event'),
			),
		),
	);

	public function search(array $params)
	{
	}

	public function setReferenceListForRelation_activeBookingEvents(&$model, $ref_list)
	{
		$booking_list = array();

		foreach ($ref_list as $i => $event_ref) {
			$id = method_exists($event_ref,'getId') ? $event_ref->getId() : $event_ref->id;

			if (!$booking = \OphTrOperationbooking_Operation_Booking::model()->with(array('operation' => array('with' => 'event')))->find('event.id=?',array($id))) {
				throw new \Exception("Booking not found for event_id: $id");
			}

			$booking->display_order = $i+1;

			$booking_list[] = $booking;
		}

		$model->setAttribute('activeBookings',$booking_list);
	}

	public function resourceToModel_AfterSave($model)
	{
		foreach ($model->expandAttribute('activeBookings') as $i => $booking) {
			if (!$booking->save()) {
				throw new \Exception("Unable to update booking display_order: ".print_r($booking->getErrors(),true));
			}
		}
	}
}
