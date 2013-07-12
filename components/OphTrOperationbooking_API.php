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

class OphTrOperationbooking_API extends BaseAPI
{
	public function getBookingsForEpisode($episode_id)
	{
		$criteria = new CDbCriteria;
		$criteria->order = 'datetime asc';
		$criteria->addCondition('episode_id',$episode_id);

		return OphTrOperationbooking_Operation_Booking::model()
			->with('session')
			->with(array(
				'operation' => array(
					'condition' => "episode_id = $episode_id",
					'with' => 'event'
				)
			))
			->findAll($criteria);
	}

	/**
	 *  Gets 'open' bookings for the specified episode
	 * A booking is deemed open if it has no operation note linked to it
	 *
	 *  @params integer $episode_id
	 *  @return OphTrOperationbooking_Operation_Booking[]
	 */
	public function getOpenBookingsForEpisode($episode_id)
	{
		$criteria = new CDbCriteria;
		$criteria->order = 'datetime asc';
		$criteria->addCondition('episode_id',$episode_id);
		$criteria->addCondition('`t`.booking_cancellation_date is null');

		$status_scheduled = OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Scheduled'));
		$status_rescheduled = OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Rescheduled'));

		return OphTrOperationbooking_Operation_Booking::model()
			->with('session')
			->with(array(
				'operation' => array(
					'condition' => "episode_id = $episode_id and status_id in ($status_scheduled->id,$status_rescheduled->id)",
					'with' => 'event'
				)
			))
			->findAll($criteria);
	}

	public function getOperationProcedures($operation_id)
	{
		return OphTrOperationbooking_Operation_Procedures::model()->findAll('element_id=?',array($operation_id));
	}

	public function getOperationForEvent($event_id)
	{
		return Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($event_id));
	}

	public function setOperationStatus($event_id, $status_name)
	{
		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($event_id))) {
			throw new Exception("Operation event not found: $event_id");
		}

		if ($status_name == 'Scheduled or Rescheduled') {
			if (OphTrOperationbooking_Operation_Booking::model()->find('element_id=? and booking_cancellation_date is not null',array($operation->id))) {
				$status_name = 'Rescheduled';
			} else {
				$status_name = 'Scheduled';
			}
		}

		if (!$status = OphTrOperationbooking_Operation_Status::model()->find('name=?',array($status_name))) {
			throw new Exception("Unknown operation status: $status_name");
		}

		If ($operation->status_id != $status->id) {
			$operation->status_id = $status->id;

			if (!$operation->save()) {
				throw new Exception("Unable to save operation: ".print_r($operation->getErrors(),true));
			}
		}
	}

	public function getProceduresForOperation($event_id)
	{
		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($event_id))) {
			throw new Exception("Operation event not found: $event_id");
		}

		return $operation->procedures;
	}

	public function getEyeForOperation($event_id)
	{
		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($event_id))) {
			throw new Exception("Operation event not found: $event_id");
		}

		return $operation->eye;
	}

	/**
	 * Get the most recent booking for the patient in the given episode
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @return OphTrOperationbooking_Operation_Booking
	 */
	public function getMostRecentBookingForEpisode($patient, $episode)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('episode_id', $episode->id);
		$criteria->order = 'datetime desc';

		return OphTrOperationbooking_Operation_Booking::model()
			->with(array(
				'operation' => array(
					'with' => 'event'
				),
			))
			->find($criteria);
	}

	/**
	 * get the procedures for this patient and episode as a string for use in correspondence
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @return string
	 */
	public function getLetterProcedures($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			$return = '';

			if ($operation = $this->getElementForLatestEventInEpisode($patient, $episode, 'Element_OphTrOperationbooking_Operation')) {
				foreach ($operation->procedures as $i => $procedure) {
					if ($i) $return .= ', ';
					$return .= $operation->eye->adjective.' '.$procedure->term;
				}
			}

			return strtolower($return);
		}
	}

	public function getAdmissionDate($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($booking = $this->getMostRecentBookingForEpisode($patient, $episode)) {
				return $booking->session->NHSDate('date');
			}
		}
	}
}
