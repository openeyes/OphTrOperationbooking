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

namespace services;

class OphTrOperationbooking_EventService_ScheduleElementTest extends \ModuleTestCase
{
	public $fixtures = array(
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'events' => 'Event',
		'event_types' => 'EventType',
		'disorders' => 'Disorder',
		'procedures' => 'Procedure',
		'element_type' => 'ElementType',
		'el_diagnosis' => 'Element_OphTrOperationbooking_Diagnosis',
		'el_operation' => 'Element_OphTrOperationbooking_Operation',
		'el_schedule' => 'Element_OphTrOperationbooking_ScheduleOperation',
		'procassign' => 'OphTrOperationbooking_Operation_Procedures',
		'bookings' => 'OphTrOperationbooking_Operation_Booking',
		'reasons' => 'OphTrOperationbooking_Operation_Cancellation_Reason',
		'theatres' => 'OphTrOperationbooking_Operation_Theatre',
		'statuses' => 'OphTrOperationbooking_Operation_Status',
		'sessions' => 'OphTrOperationbooking_Operation_Session',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
		'specialties' => 'Specialty',
		'subspecialties' => 'Subspecialty',
		'rtts' => 'RTT',
		'rf_type' => 'ReferralType',
		'rf' => 'Referral',
		'options' => 'OphTrOperationbooking_ScheduleOperation_Options',
		'dls' => 'OphTrOperationbooking_Operation_Date_Letter_Sent',
		'reasons' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',
		'unavail' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailable',
		'priorities' => 'OphTrOperationbooking_Operation_Priority',
		'erods' => 'OphTrOperationbooking_Operation_EROD',
	);

	public function testModelToResource()
	{
		$event = $this->events('event6');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;

		$resource = $ps->modelToResource($event);

		$this->compareScheduleElementResource($resource->elements[2], $event->elements[2]);
	}

	public function compareScheduleElementResource($resource, $model)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\Element_OphTrOperationbooking_ScheduleOperation',$resource);
		$this->assertEquals($model->id,$resource->getId());

		$this->compareResourceReferences($resource, $model, array('schedule_options'));
	}

	public function getResource()
	{
		return \Yii::app()->service->Event(6)->fetch();
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, new \Event, false);

		$this->compareScheduleElementModel($event->elements[2],$resource->elements[2]);
	}

	public function compareScheduleElementModel($model, $resource)
	{
		$this->assertInstanceOf('Element_OphTrOperationbooking_ScheduleOperation',$model);

		if ($resource->getId() !== null) {
			$this->assertEquals($resource->getId(),$model->id);
		}

		$this->compareModelReferences($model, $resource, array('schedule_options'));
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->elements[0]->setId(null);
		$resource->elements[1]->setId(null);
		$resource->elements[2]->setId(null);

		foreach ($resource->elements[1]->allBookings as $i => $booking) {
			$resource->elements[1]->allBookings[$i]->setId(null);
			if ($resource->elements[1]->allBookings[$i]->erod) {
				$resource->elements[1]->allBookings[$i]->erod->setId(null);
			}
		}

		$resource->elements[1]->anaesthetic_type = 'LAC';

		$pu = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailable;
		$pu->_class_name = 'OphTrOperationbooking_ScheduleOperation_PatientUnavailable';
		$pu->start_date = new Date('2015-01-01');
		$pu->end_date = new Date('2015-01-02');
		$pu->reason_ref = \Yii::app()->service->OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason(3);

		$resource->elements[2]->patient_unavailables = array($pu);

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$resource->setId(null);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, new \Event);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, new \Event);
		$event = \Event::model()->findByPk($event->id);

		$this->assertInstanceOf('Event',$event);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function getModifiedResource()
	{
		$resource = $this->getResource();

		$resource->info = 'blah blah blah';
		$resource->delete_reason = 'because its testing';
		$resource->delete_pending = 1;

		$resource->elements[0]->eye = 'Left';
		$resource->elements[0]->disorder_ref = \Yii::app()->service->Disorder(8);

		$resource->elements[1]->consultant_required = 0;
		$resource->elements[1]->overnight_stay = 1;
		$resource->elements[1]->comments = 'i am testing';
		$resource->elements[1]->total_duration = 60;
		$resource->elements[1]->anaesthetist_required = 0;
		$resource->elements[1]->comments_rtt = 'more RTT comments';
		$resource->elements[1]->eye = 'Right';
		$resource->elements[1]->anaesthetic_type = 'Topical';
		$resource->elements[1]->priority = 'Urgent';
		$resource->elements[1]->status = 'Rescheduled';
		$resource->elements[1]->latest_booking_id = 3;
		$resource->elements[1]->date_invitation_letter_sent = '2013-01-01 12:00:00';
		$resource->elements[1]->date_1st_reminder_letter_sent = '2013-02-02 12:00:00';
		$resource->elements[1]->date_2nd_reminder_letter_sent = '2013-03-02 12:00:00';
		$resource->elements[1]->date_gp_letter_sent = '2013-04-02 12:00:00';
		$resource->elements[1]->date_scheduling_letter_sent = '2013-05-02 12:00:00';
		$resource->elements[1]->procedures[0] = \Yii::app()->service->Procedure(2);
		$resource->elements[1]->procedures[1] = \Yii::app()->service->Procedure(1);
		$resource->elements[1]->allBookings[0]->session_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Session(10);
		$resource->elements[1]->allBookings[0]->transport_arranged = 1;
		$resource->elements[1]->allBookings[0]->transport_arranged_date = '2013-04-04';
		$resource->elements[1]->allBookings[0]->ward_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Ward(2);
		$resource->elements[1]->allBookings[0]->session_theatre_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(2);
		unset($resource->elements[1]->allBookings[1]);
		$resource->elements[1]->latest_booking_id = $resource->elements[1]->allBookings[0]->getId();

		return $resource;
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		// Ensure existing display_order is preserved
		$eo = \Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($this->events('event6')->id));
		$booking = $eo->allBookings[0];
		$booking->display_order = 19;
		$booking->save();
		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, $this->events('event6'));

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		// Ensure existing display_order is preserved
		$eo = \Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($this->events('event6')->id));
		$booking = $eo->allBookings[0];
		$booking->display_order = 19;
		$booking->save();

		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, $this->events('event6'));
		$event = \Event::model()->findByPk($event->id);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testJsonToResource()
	{
		$event = \Event::model()->findByPk(6);
		$resource = \Yii::app()->service->OphTrOperationbooking_Event($event->id)->fetch();

		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$resource = $ps->jsonToResource($json);

		$this->compareScheduleElementResource($resource->elements[2], $event->elements[2]);
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event, false);

		$this->compareScheduleElementModel($event->elements[2],$resource->elements[2]);
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event);
		$event = \Event::model()->findByPk($event->id);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		// Ensure existing display_order is preserved
		$eo = \Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($this->events('event6')->id));
		$booking = $eo->allBookings[0];
		$booking->display_order = 19;
		$booking->save();

		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, $this->events('event6'));

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		// Ensure existing display_order is preserved
		$eo = \Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($this->events('event6')->id));
		$booking = $eo->allBookings[0];
		$booking->display_order = 19;
		$booking->save();

		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, $this->events('event6'));
		$event = \Event::model()->findByPk($event->id);

		$this->compareScheduleElementModel($event->elements[2], $resource->elements[2]);
	}
}
