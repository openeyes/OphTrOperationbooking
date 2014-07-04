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

class OphTrOperationbooking_Operation_BookingService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'OphTrOperationbooking_Operation_Booking';

	static protected $model_map = array(
		'OphTrOperationbooking_Operation_Booking' => array(
			'related_objects' => array(
				'element' => array('element_id', 'Element_OphTrOperationbooking_Operation'),
			),
			'reference_objects' => array(
				'cancellationReason' => array('cancellation_reason_id', 'OphTrOperationbooking_Operation_Cancellation_Reason', array('text')),
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
				'cancellation_reason' => 'cancellationReason.text',
				'cancellation_comment' => 'cancellation_comment',
				'cancellation_user' => array(self::TYPE_REF, 'cancellation_user_id', 'User'),
			),
		),
	);

	public function search(array $params)
	{
	}
}
