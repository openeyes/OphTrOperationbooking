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

class OphTrOperationbooking_EventService_OperationElementTest extends \ModuleTestCase
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

		$this->compareOperationElementResource($resource->elements[1], $event->elements[1]);
	}

	public function compareOperationElementResource($resource, $model)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\Element_OphTrOperationbooking_Operation',$resource);
		$this->assertEquals($model->id,$resource->getId());

		$this->compareResourceFields($resource, $model, array(
				'eye','consultant_required','anaesthetic_type','overnight_stay','priority','decision_date','comments',
				'total_duration','status','anaesthetist_required','operation_cancellation_date','cancellation_reason',
				'cancellation_comment','latest_booking_id','comments_rtt'
		));

		$this->compareResourceReferences($resource, $model, array('site','cancellation_user','referral','rtt'));

		$this->compareResourceFields($resource, $model->date_letter_sent, array(
				'date_invitation_letter_sent','date_invitation_letter_sent','date_2nd_reminder_letter_sent','date_gp_letter_sent',
				'date_scheduling_letter_sent'
		));

		$this->assertCount(1,$resource->procedures);
		$this->assertInstanceOf('services\ProcedureReference',$resource->procedures[0]);
		$this->assertEquals('Foobar Procedure',$resource->procedures[0]->fetch()->term);

		$this->assertCount(2,$resource->allBookings);

		foreach ($resource->allBookings as $i => $booking) {
			$this->compareBookingResource($resource->allBookings[$i],$model->allBookings[$i]);
		}
	}

	public function compareBookingResource($resource, $model)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Booking',$resource);
		$this->assertEquals($model->id,$resource->getId());

		$this->compareResourceFields($resource, $model, array(
				'admission_time','confirmed','session_date','session_start_time','session_end_time','transport_arranged',
				'booking_cancellation_date','cancellationReason','cancellation_comment'
		));

		$this->compareResourceReferences($resource, $model, array('session','ward','session_theatre','cancellation_user'));

		if (is_null($model->erod)) {
			$this->assertNull($resource->erod);
		} else {
			$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_EROD',$resource->erod);

			$this->compareResourceFields($resource->erod,$model->erod,array(
					'session_date','session_start_time','session_end_time','consultant','paediatric','anaesthetist','general_anaesthetic','session_duration',
					'total_operations_time','available_time'
			));

			$this->compareResourceReferences($resource->erod,$model->erod,array('session','firm'));
		}
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

		$this->compareOperationElementModel($event->elements[1],$resource->elements[1]);
	}

	public function compareOperationElementModel($model, $resource)
	{
		$this->assertInstanceOf('Element_OphTrOperationbooking_Operation',$model);

		if ($resource->getId() !== null) {
			$this->assertEquals($resource->getId(),$model->id);
		}

		$this->compareModelFields($model, $resource, array(
				'eye','consultant_required','anaesthetic_type','overnight_stay','priority','decision_date','comments',
				'total_duration','status','anaesthetist_required','operation_cancellation_date','cancellation_reason',
				'cancellation_comment','latest_booking_id','comments_rtt'
		));

		$this->compareModelReferences($model, $resource, array('site','cancellation_user','referral','rtt'));

		$this->compareModelFields($model->date_letter_sent, $resource, array(
				'date_invitation_letter_sent','date_invitation_letter_sent','date_2nd_reminder_letter_sent','date_gp_letter_sent',
				'date_scheduling_letter_sent'
		));

		$this->assertCount(count($resource->procedures),$model->procedures);

		foreach ($resource->procedures as $i => $procedure) {
			$this->assertInstanceOf('Procedure',$model->procedures[$i]);
			$this->assertEquals($procedure->getId(),$model->procedures[$i]->id);
			$this->assertEquals($procedure->fetch()->term,$model->procedures[$i]->term);
		}

		$this->assertCount(count($resource->allBookings),$model->allBookings);

		foreach ($model->allBookings as $i => $booking) {
			$this->compareBookingModel($model->allBookings[$i],$resource->allBookings[$i]);
		}
	}

	public function compareBookingModel($model, $resource)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$model);

		if ($resource->getId() !== null) {
			$this->assertEquals($resource->getId(),$model->id);
		}

		$this->compareModelFields($model, $resource, array(
				'admission_time','confirmed','session_date','session_start_time','session_end_time','transport_arranged',
				'booking_cancellation_date','cancellationReason','cancellation_comment'
		));

		$this->compareModelReferences($model, $resource, array('session','ward','session_theatre','cancellation_user'));

		if (is_null($resource->erod)) {
			$this->assertNull($model->erod);
		} else {
			$this->assertInstanceOf('OphTrOperationbooking_Operation_EROD',$model->erod);

			$this->compareModelFields($model->erod,$resource->erod,array(
					'session_date','session_start_time','session_end_time','consultant','paediatric','anaesthetist','general_anaesthetic','session_duration',
					'total_operations_time','available_time'
			));

			$this->compareModelReferences($model->erod,$resource->erod,array('session','firm'));
		}
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

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->resourceToModel($resource, new \Event);
		$event = \Event::model()->findByPk($event->id);

		$this->assertInstanceOf('Event',$event);

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
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

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
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

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
	}

	public function testJsonToResource()
	{
		$event = \Event::model()->findByPk(6);
		$resource = \Yii::app()->service->OphTrOperationbooking_Event($event->id)->fetch();

		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$resource = $ps->jsonToResource($json);

		$this->compareOperationElementResource($resource->elements[1], $event->elements[1]);
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event, false);

		$this->compareOperationElementModel($event->elements[1],$resource->elements[1]);
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event);

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_EventService;
		$event = $ps->jsonToModel($json, new \Event);
		$event = \Event::model()->findByPk($event->id);

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
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

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
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

		$this->compareOperationElementModel($event->elements[1], $resource->elements[1]);
	}
}
