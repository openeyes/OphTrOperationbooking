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

class OphTrOperationbooking_EventReference extends \services\EventReference
{
	public function schedule($resource)
	{
		foreach ($resource->elements[1]->allBookings as $_booking) {
			if ($_booking->booking_cancellation_date === null) {
				if (isset($activeBooking)) {
					throw new \Exception("Multiple active bookings passed in resource to schedule()");
				}
				$activeBooking = $_booking;
			}
		}

		if (!isset($resource)) {
			throw new \Exception("Schedule called with a resource that contains no active booking");
		}

		// Save the operation event
		$event = \Event::model()->findByPk($this->getId());

		$es = new OphTrOperationbooking_EventService;
		$event = $es->resourceToModel($resource, $event);

		$booking = new \OphTrOperationbooking_Operation_Booking;
		$parser = new \services\DeclarativeTypeParser_Elements(new \services\ModelConverter($es));

		$parser->resourceToModelParse_Fields($booking, $activeBooking);
		$parser->resourceToModelParse_Relations($booking, $activeBooking, null, null);
		$parser->resourceToModelParse_References($booking, $activeBooking);

		$operation = $event->elements[1];

		$booking->element_id = $operation->id;

		if (!$booking->save()) {
			throw new \Exception("Unable to save schedule operation: ".print_r($booking->getErrors(),true));
		}

		$operation->latest_booking_id = $booking->id;

		if (!$operation->save()) {
			throw new \Exception("Unable to set latest_booking_id on operation element: ".print_r($operation->getErrors(),true));
		}

		return $event;
	}
}
