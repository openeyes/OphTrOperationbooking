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

class EventServiceTest extends \CDbTestCase
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
	);

	public function testModelToResource()
	{
		$event = $this->events('event6');

		$ps = new EventService;

		$resource = $ps->modelToResource($event);

		$this->assertInstanceOf('services\Event',$resource);
		$this->assertInstanceOf('services\EpisodeReference',$resource->episode_ref);
		$this->assertEquals(4,$resource->episode_ref->getId());
		$this->assertInstanceOf('services\EventTypeReference',$resource->event_type_ref);
		$this->assertEquals(1001,$resource->event_type_ref->getId());
		$this->assertInstanceOf('services\Date',$resource->created_date);
		$this->assertInstanceOf('services\Date',$resource->event_date);
		$this->assertEquals('someinfo',$resource->info);
		$this->assertEquals(0,$resource->deleted);
		$this->assertNull($resource->delete_reason);
		$this->assertEquals(0,$resource->delete_pending);

		$this->assertCount(3,$resource->elements);

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\Element_OphTrOperationbooking_Diagnosis',$resource->elements[0]);
		$this->assertEquals($event->elements[0]->id,$resource->elements[0]->getId());
		$this->assertEquals('Right',$resource->elements[0]->eye);
		$this->assertInstanceOf('services\DisorderReference',$resource->elements[0]->disorder_ref);
		$this->assertEquals('Retinal lattice degeneration',$resource->elements[0]->disorder_ref->fetch()->term);

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\Element_OphTrOperationbooking_Operation',$resource->elements[1]);
		$this->assertEquals($event->elements[1]->id,$resource->elements[1]->getId());
		$this->assertEquals('Left',$resource->elements[1]->eye);
		$this->assertEquals(1,$resource->elements[1]->consultant_required);
		$this->assertEquals('GA',$resource->elements[1]->anaesthetic_type);
		$this->assertEquals(0,$resource->elements[1]->overnight_stay);
		$this->assertInstanceOf('services\SiteReference',$resource->elements[1]->site_ref);
		$this->assertEquals(1,$resource->elements[1]->site_ref->getId());
		$this->assertEquals('Routine',$resource->elements[1]->priority);
		$this->assertInstanceOf('services\Date',$resource->elements[1]->decision_date);
		$this->assertEquals('Test comments',$resource->elements[1]->comments);
		$this->assertEquals(100,$resource->elements[1]->total_duration);
		$this->assertEquals('Scheduled',$resource->elements[1]->status);
		$this->assertEquals(1,$resource->elements[1]->anaesthetist_required);
		$this->assertNull($resource->elements[1]->operation_cancellation_date);
		$this->assertNull($resource->elements[1]->cancellation_user_ref->getId());
		$this->assertNull($resource->elements[1]->cancellation_reason);
		$this->assertEquals('',$resource->elements[1]->cancellation_comment);
		$this->assertEquals(2,$resource->elements[1]->latest_booking_id);
		$this->assertEquals('these are RTT comments',$resource->elements[1]->comments_rtt);
		$this->assertInstanceOf('services\ReferralReference',$resource->elements[1]->referral_ref);
		$this->assertEquals(5,$resource->elements[1]->referral_ref->getId());
		$this->assertInstanceOf('services\RTTReference',$resource->elements[1]->rtt_ref);
		$this->assertEquals(1,$resource->elements[1]->rtt_ref->getId());

		$this->assertCount(1,$resource->elements[1]->procedures);
		$this->assertInstanceOf('services\ProcedureReference',$resource->elements[1]->procedures[0]);
		$this->assertEquals('Foobar Procedure',$resource->elements[1]->procedures[0]->fetch()->term);

		$this->assertCount(2,$resource->elements[1]->allBookings);

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Booking',$resource->elements[1]->allBookings[0]);
		$this->assertEquals($event->elements[1]->allBookings[0]->id,$resource->elements[1]->allBookings[0]->getId());
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionReference',$resource->elements[1]->allBookings[0]->session_ref);
		$this->assertEquals(5,$resource->elements[1]->allBookings[0]->session_ref->getId());
		$this->assertEquals(1,$resource->elements[1]->allBookings[0]->display_order);
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardReference',$resource->elements[1]->allBookings[0]->ward_ref);
		$this->assertEquals(1,$resource->elements[1]->allBookings[0]->ward_ref->getId());
		$this->assertEquals('08:00:00',$resource->elements[1]->allBookings[0]->admission_time);
		$this->assertEquals(1,$resource->elements[1]->allBookings[0]->confirmed);
		$this->assertInstanceOf('services\Date',$resource->elements[1]->allBookings[0]->session_date);
		$this->assertEquals('08:00:00',$resource->elements[1]->allBookings[0]->session_start_time);
		$this->assertEquals('13:00:00',$resource->elements[1]->allBookings[0]->session_end_time);
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->elements[1]->allBookings[0]->session_theatre_ref);
		$this->assertEquals(1,$resource->elements[1]->allBookings[0]->session_theatre_ref->getId());
		$this->assertEquals(0,$resource->elements[1]->allBookings[0]->transport_arranged);
		$this->assertNull($resource->elements[1]->allBookings[0]->transport_arranged_date);
		$this->assertNull($resource->elements[1]->allBookings[0]->booking_cancellation_date);
		$this->assertNull($resource->elements[1]->allBookings[0]->cancellationReason);
		$this->assertEquals('',$resource->elements[1]->allBookings[0]->cancellation_comment);
		$this->assertNull($resource->elements[1]->allBookings[0]->cancellation_user_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Booking',$resource->elements[1]->allBookings[1]);
		$this->assertEquals($event->elements[1]->allBookings[1]->id,$resource->elements[1]->allBookings[1]->getId());
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionReference',$resource->elements[1]->allBookings[1]->session_ref);
		$this->assertEquals(3,$resource->elements[1]->allBookings[1]->session_ref->getId());
		$this->assertEquals(2,$resource->elements[1]->allBookings[1]->display_order);
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardReference',$resource->elements[1]->allBookings[1]->ward_ref);
		$this->assertEquals(2,$resource->elements[1]->allBookings[1]->ward_ref->getId());
		$this->assertEquals('08:00:00',$resource->elements[1]->allBookings[1]->admission_time);
		$this->assertEquals(1,$resource->elements[1]->allBookings[1]->confirmed);
		$this->assertInstanceOf('services\Date',$resource->elements[1]->allBookings[1]->session_date);
		$this->assertEquals('08:00:00',$resource->elements[1]->allBookings[1]->session_start_time);
		$this->assertEquals('13:00:00',$resource->elements[1]->allBookings[1]->session_end_time);
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->elements[1]->allBookings[1]->session_theatre_ref);
		$this->assertEquals(1,$resource->elements[1]->allBookings[1]->session_theatre_ref->getId());
		$this->assertEquals(1,$resource->elements[1]->allBookings[1]->transport_arranged);
		$this->assertInstanceOf('services\Date',$resource->elements[1]->allBookings[1]->transport_arranged_date);
		$this->assertInstanceOf('services\DateTime',$resource->elements[1]->allBookings[1]->booking_cancellation_date);
		$this->assertEquals('ran out of biros',$resource->elements[1]->allBookings[1]->cancellationReason);
		$this->assertEquals('cancelled due to biro shortage',$resource->elements[1]->allBookings[1]->cancellation_comment);
		$this->assertInstanceOf('services\UserReference',$resource->elements[1]->allBookings[1]->cancellation_user_ref);
		$this->assertEquals(1,$resource->elements[1]->allBookings[1]->cancellation_user_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\Element_OphTrOperationbooking_ScheduleOperation',$resource->elements[2]);
		$this->assertEquals($event->elements[2]->id,$resource->elements[2]->getId());
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsReference',$resource->elements[2]->schedule_options_ref);
		$this->assertEquals('As soon as possible',$resource->elements[2]->schedule_options_ref->fetch()->name);
	}

	public function getResource()
	{
		return \Yii::app()->service->Event(6)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$total_events = count(\Event::model()->findAll());
		$total_eo = count(\Element_OphTrOperationbooking_Operation::model()->findAll());
		$total_di = count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll());
		$total_sh = count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll());

		$ps = new EventService;
		$patient = $ps->resourceToModel($resource, new \Event, false);

		$this->assertEquals($total_events, count(\Event::model()->findAll()));
		$this->assertEquals($total_eo, count(\Element_OphTrOperationbooking_Operation::model()->findAll()));
		$this->assertEquals($total_di, count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll()));
		$this->assertEquals($total_sh, count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, new \Event, false);

		$this->assertInstanceOf('Event',$event);

		$this->assertCount(3,$event->elements);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Diagnosis',$event->elements[0]);
		$this->assertEquals($resource->elements[0]->getId(),$event->elements[0]->id);
		$this->assertEquals(\Eye::model()->find('name=?',array('Right'))->id,$event->elements[0]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[0]->eye);
		$this->assertEquals('Right',$event->elements[0]->eye->name);
		$this->assertEquals(\Disorder::model()->find('term=?',array('Retinal lattice degeneration'))->id,$event->elements[0]->disorder_id);
		$this->assertInstanceOf('Disorder',$event->elements[0]->disorder);
		$this->assertEquals('Retinal lattice degeneration',$event->elements[0]->disorder->term);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Operation',$event->elements[1]);
		$this->assertEquals($resource->elements[1]->getId(),$event->elements[1]->id);
		$this->assertEquals(\Eye::model()->find('name=?',array('Left'))->id,$event->elements[1]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[1]->eye);
		$this->assertEquals('Left',$event->elements[1]->eye->name);
		$this->assertEquals(1,$event->elements[1]->consultant_required);
		$this->assertEquals(\AnaestheticType::model()->find('name=?',array('GA'))->id,$event->elements[1]->anaesthetic_type_id);
		$this->assertInstanceOf('AnaestheticType',$event->elements[1]->anaesthetic_type);
		$this->assertEquals('GA',$event->elements[1]->anaesthetic_type->name);
		$this->assertEquals(0,$event->elements[1]->overnight_stay);
		$this->assertEquals(1,$event->elements[1]->site_id);
		$this->assertInstanceOf('Site',$event->elements[1]->site);
		$this->assertEquals(1,$event->elements[1]->site->id);
		$this->assertEquals(\OphTrOperationbooking_Operation_Priority::model()->find('name=?',array('Routine'))->id,$event->elements[1]->priority_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$event->elements[1]->priority);
		$this->assertEquals('Routine',$event->elements[1]->priority->name);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->decision_date);
		$this->assertEquals('Test comments',$event->elements[1]->comments);
		$this->assertEquals(100,$event->elements[1]->total_duration);
		$this->assertEquals(\OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Scheduled'))->id,$event->elements[1]->status_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Status',$event->elements[1]->status);
		$this->assertEquals('Scheduled',$event->elements[1]->status->name);
		$this->assertEquals(1,$event->elements[1]->anaesthetist_required);
		$this->assertNull($event->elements[1]->operation_cancellation_date);
		$this->assertNull($event->elements[1]->cancellation_user);
		$this->assertNull($event->elements[1]->cancellation_reason);
		$this->assertEquals('',$event->elements[1]->cancellation_comment);
		$this->assertEquals(2,$event->elements[1]->latest_booking_id);
		$this->assertEquals('these are RTT comments',$event->elements[1]->comments_rtt);
		$this->assertInstanceOf('Referral',$event->elements[1]->referral);
		$this->assertEquals(5,$event->elements[1]->referral->id);
		$this->assertInstanceOf('RTT',$event->elements[1]->rtt);
		$this->assertEquals(1,$event->elements[1]->rtt->id);

		$this->assertCount(1,$event->elements[1]->procedures);
		$this->assertInstanceOf('Procedure',$event->elements[1]->procedures[0]);
		$this->assertEquals(\Procedure::model()->find('term=?',array('Foobar Procedure'))->id,$event->elements[1]->procedures[0]->id);
		$this->assertEquals('Foobar Procedure',$event->elements[1]->procedures[0]->term);

		$this->assertCount(2,$event->elements[1]->allBookings);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[0]);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[0]->session);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->display_order);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[0]->ward);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[0]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[0]->session_end_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[0]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre->id);
		$this->assertEquals(0,$event->elements[1]->allBookings[0]->transport_arranged);
		$this->assertNull($event->elements[1]->allBookings[0]->transport_arranged_date);
		$this->assertNull($event->elements[1]->allBookings[0]->booking_cancellation_date);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellationReason);
		$this->assertEquals('',$event->elements[1]->allBookings[0]->cancellation_comment);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellation_user_id);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[1]);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[1]->session);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session_id);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session->id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->display_order);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[1]->ward);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward_id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[1]->session_end_time);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[1]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->transport_arranged);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->transport_arranged_date);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$event->elements[1]->allBookings[1]->booking_cancellation_date);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Cancellation_Reason',$event->elements[1]->allBookings[1]->cancellationReason);
		$this->assertEquals('ran out of biros',$event->elements[1]->allBookings[1]->cancellationReason->name);
		$this->assertEquals('cancelled due to biro shortage',$event->elements[1]->allBookings[1]->cancellation_comment);
		$this->assertInstanceOf('User',$event->elements[1]->allBookings[1]->cancellation_user);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user->id);

		$this->assertInstanceOf('Element_OphTrOperationbooking_ScheduleOperation',$event->elements[2]);
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$event->elements[2]->schedule_options);
		$this->assertEquals('As soon as possible',$event->elements[2]->schedule_options->name);
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getResource();

		$resource->elements[0]->setId(null);
		$resource->elements[1]->setId(null);
		$resource->elements[2]->setId(null);

		$resource->elements[1]->allBookings[0]->setId(null);
		$resource->elements[1]->allBookings[1]->setId(null);

		$resource->elements[1]->anaesthetic_type = 'Topical';

		$total_events = count(\Event::model()->findAll());
		$total_eo = count(\Element_OphTrOperationbooking_Operation::model()->findAll());
		$total_di = count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll());
		$total_sh = count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll());
		$total_b = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, new \Event);

		$this->assertEquals($total_events+1, count(\Event::model()->findAll()));
		$this->assertEquals($total_eo+1, count(\Element_OphTrOperationbooking_Operation::model()->findAll()));
		$this->assertEquals($total_di+1, count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll()));
		$this->assertEquals($total_sh+1, count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll()));
		$this->assertEquals($total_b+2, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$resource->elements[0]->setId(null);
		$resource->elements[1]->setId(null);
		$resource->elements[2]->setId(null);

		$resource->elements[1]->allBookings[0]->setId(null);
		$resource->elements[1]->allBookings[1]->setId(null);

		$resource->elements[1]->anaesthetic_type = 'Topical';

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, new \Event);

		$this->assertInstanceOf('Event',$event);

		$this->assertCount(3,$event->elements);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Diagnosis',$event->elements[0]);
		$this->assertEquals(\Eye::model()->find('name=?',array('Right'))->id,$event->elements[0]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[0]->eye);
		$this->assertEquals('Right',$event->elements[0]->eye->name);
		$this->assertEquals(\Disorder::model()->find('term=?',array('Retinal lattice degeneration'))->id,$event->elements[0]->disorder_id);
		$this->assertInstanceOf('Disorder',$event->elements[0]->disorder);
		$this->assertEquals('Retinal lattice degeneration',$event->elements[0]->disorder->term);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Operation',$event->elements[1]);
		$this->assertEquals(\Eye::model()->find('name=?',array('Left'))->id,$event->elements[1]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[1]->eye);
		$this->assertEquals('Left',$event->elements[1]->eye->name);
		$this->assertEquals(1,$event->elements[1]->consultant_required);
		$this->assertEquals(\AnaestheticType::model()->find('name=?',array('Topical'))->id,$event->elements[1]->anaesthetic_type_id);
		$this->assertInstanceOf('AnaestheticType',$event->elements[1]->anaesthetic_type);
		$this->assertEquals('Topical',$event->elements[1]->anaesthetic_type->name);
		$this->assertEquals(0,$event->elements[1]->overnight_stay);
		$this->assertEquals(1,$event->elements[1]->site_id);
		$this->assertInstanceOf('Site',$event->elements[1]->site);
		$this->assertEquals(1,$event->elements[1]->site->id);
		$this->assertEquals(\OphTrOperationbooking_Operation_Priority::model()->find('name=?',array('Routine'))->id,$event->elements[1]->priority_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$event->elements[1]->priority);
		$this->assertEquals('Routine',$event->elements[1]->priority->name);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->decision_date);
		$this->assertEquals('Test comments',$event->elements[1]->comments);
		$this->assertEquals(100,$event->elements[1]->total_duration);
		$this->assertEquals(\OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Scheduled'))->id,$event->elements[1]->status_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Status',$event->elements[1]->status);
		$this->assertEquals('Scheduled',$event->elements[1]->status->name);
		$this->assertEquals(0,$event->elements[1]->anaesthetist_required);
		$this->assertNull($event->elements[1]->operation_cancellation_date);
		$this->assertNull($event->elements[1]->cancellation_user);
		$this->assertNull($event->elements[1]->cancellation_reason);
		$this->assertEquals('',$event->elements[1]->cancellation_comment);
		$this->assertEquals(2,$event->elements[1]->latest_booking_id);
		$this->assertEquals('these are RTT comments',$event->elements[1]->comments_rtt);
		$this->assertInstanceOf('Referral',$event->elements[1]->referral);
		$this->assertEquals(5,$event->elements[1]->referral->id);
		$this->assertInstanceOf('RTT',$event->elements[1]->rtt);
		$this->assertEquals(1,$event->elements[1]->rtt->id);

		$this->assertCount(1,$event->elements[1]->procedure_assignment);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Procedures',$event->elements[1]->procedure_assignment[0]);
		$this->assertEquals(\Procedure::model()->find('term=?',array('Foobar Procedure'))->id,$event->elements[1]->procedure_assignment[0]->proc_id);
		$this->assertEquals('Foobar Procedure',$event->elements[1]->procedure_assignment[0]->proc->term);
		$this->assertEquals(0,$event->elements[1]->procedure_assignment[0]->display_order);

		$this->assertCount(2,$event->elements[1]->allBookings);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[0]);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[0]->session);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->display_order);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[0]->ward);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[0]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[0]->session_end_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[0]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre->id);
		$this->assertEquals(0,$event->elements[1]->allBookings[0]->transport_arranged);
		$this->assertNull($event->elements[1]->allBookings[0]->transport_arranged_date);
		$this->assertNull($event->elements[1]->allBookings[0]->booking_cancellation_date);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellationReason);
		$this->assertEquals('',$event->elements[1]->allBookings[0]->cancellation_comment);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellation_user_id);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[1]);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[1]->session);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session_id);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session->id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->display_order);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[1]->ward);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward_id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[1]->session_end_time);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[1]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->transport_arranged);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->transport_arranged_date);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$event->elements[1]->allBookings[1]->booking_cancellation_date);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Cancellation_Reason',$event->elements[1]->allBookings[1]->cancellationReason);
		$this->assertEquals('ran out of biros',$event->elements[1]->allBookings[1]->cancellationReason->name);
		$this->assertEquals('cancelled due to biro shortage',$event->elements[1]->allBookings[1]->cancellation_comment);
		$this->assertInstanceOf('User',$event->elements[1]->allBookings[1]->cancellation_user);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user->id);

		$this->assertInstanceOf('Element_OphTrOperationbooking_ScheduleOperation',$event->elements[2]);
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$event->elements[2]->schedule_options);
		$this->assertEquals('As soon as possible',$event->elements[2]->schedule_options->name);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getResource();

		$resource->elements[0]->setId(null);
		$resource->elements[1]->setId(null);
		$resource->elements[2]->setId(null);

		$resource->elements[1]->allBookings[0]->setId(null);
		$resource->elements[1]->allBookings[1]->setId(null);

		$resource->elements[1]->anaesthetic_type = 'Topical';

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, new \Event);
		$event = \Event::model()->findByPk($event->id);

		$this->assertInstanceOf('Event',$event);

		$this->assertCount(3,$event->elements);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Diagnosis',$event->elements[0]);
		$this->assertEquals(\Eye::model()->find('name=?',array('Right'))->id,$event->elements[0]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[0]->eye);
		$this->assertEquals('Right',$event->elements[0]->eye->name);
		$this->assertEquals(\Disorder::model()->find('term=?',array('Retinal lattice degeneration'))->id,$event->elements[0]->disorder_id);
		$this->assertInstanceOf('Disorder',$event->elements[0]->disorder);
		$this->assertEquals('Retinal lattice degeneration',$event->elements[0]->disorder->term);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Operation',$event->elements[1]);
		$this->assertEquals(\Eye::model()->find('name=?',array('Left'))->id,$event->elements[1]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[1]->eye);
		$this->assertEquals('Left',$event->elements[1]->eye->name);
		$this->assertEquals(1,$event->elements[1]->consultant_required);
		$this->assertEquals(\AnaestheticType::model()->find('name=?',array('Topical'))->id,$event->elements[1]->anaesthetic_type_id);
		$this->assertInstanceOf('AnaestheticType',$event->elements[1]->anaesthetic_type);
		$this->assertEquals('Topical',$event->elements[1]->anaesthetic_type->name);
		$this->assertEquals(0,$event->elements[1]->overnight_stay);
		$this->assertEquals(1,$event->elements[1]->site_id);
		$this->assertInstanceOf('Site',$event->elements[1]->site);
		$this->assertEquals(1,$event->elements[1]->site->id);
		$this->assertEquals(\OphTrOperationbooking_Operation_Priority::model()->find('name=?',array('Routine'))->id,$event->elements[1]->priority_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$event->elements[1]->priority);
		$this->assertEquals('Routine',$event->elements[1]->priority->name);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->decision_date);
		$this->assertEquals('Test comments',$event->elements[1]->comments);
		$this->assertEquals(100,$event->elements[1]->total_duration);
		$this->assertEquals(\OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Scheduled'))->id,$event->elements[1]->status_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Status',$event->elements[1]->status);
		$this->assertEquals('Scheduled',$event->elements[1]->status->name);
		$this->assertEquals(0,$event->elements[1]->anaesthetist_required);
		$this->assertNull($event->elements[1]->operation_cancellation_date);
		$this->assertNull($event->elements[1]->cancellation_user);
		$this->assertNull($event->elements[1]->cancellation_reason);
		$this->assertEquals('',$event->elements[1]->cancellation_comment);
		$this->assertEquals(2,$event->elements[1]->latest_booking_id);
		$this->assertEquals('these are RTT comments',$event->elements[1]->comments_rtt);
		$this->assertInstanceOf('Referral',$event->elements[1]->referral);
		$this->assertEquals(5,$event->elements[1]->referral->id);
		$this->assertInstanceOf('RTT',$event->elements[1]->rtt);
		$this->assertEquals(1,$event->elements[1]->rtt->id);

		$this->assertCount(1,$event->elements[1]->procedure_assignment);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Procedures',$event->elements[1]->procedure_assignment[0]);
		$this->assertEquals(\Procedure::model()->find('term=?',array('Foobar Procedure'))->id,$event->elements[1]->procedure_assignment[0]->proc_id);
		$this->assertEquals('Foobar Procedure',$event->elements[1]->procedure_assignment[0]->proc->term);
		$this->assertEquals(0,$event->elements[1]->procedure_assignment[0]->display_order);

		$this->assertCount(2,$event->elements[1]->allBookings);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[0]);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[0]->session);
		$this->assertEquals(5,$event->elements[1]->allBookings[0]->session->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->display_order);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[0]->ward);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[0]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[0]->session_end_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[0]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->session_theatre->id);
		$this->assertEquals(0,$event->elements[1]->allBookings[0]->transport_arranged);
		$this->assertNull($event->elements[1]->allBookings[0]->transport_arranged_date);
		$this->assertNull($event->elements[1]->allBookings[0]->booking_cancellation_date);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellationReason);
		$this->assertEquals('',$event->elements[1]->allBookings[0]->cancellation_comment);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellation_user_id);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[1]);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[1]->session);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session_id);
		$this->assertEquals(3,$event->elements[1]->allBookings[1]->session->id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->display_order);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[1]->ward);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward_id);
		$this->assertEquals(2,$event->elements[1]->allBookings[1]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[1]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[1]->session_end_time);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[1]->session_theatre);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->session_theatre->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->transport_arranged);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[1]->transport_arranged_date);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$event->elements[1]->allBookings[1]->booking_cancellation_date);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Cancellation_Reason',$event->elements[1]->allBookings[1]->cancellationReason);
		$this->assertEquals('ran out of biros',$event->elements[1]->allBookings[1]->cancellationReason->name);
		$this->assertEquals('cancelled due to biro shortage',$event->elements[1]->allBookings[1]->cancellation_comment);
		$this->assertInstanceOf('User',$event->elements[1]->allBookings[1]->cancellation_user);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user_id);
		$this->assertEquals(1,$event->elements[1]->allBookings[1]->cancellation_user->id);

		$this->assertInstanceOf('Element_OphTrOperationbooking_ScheduleOperation',$event->elements[2]);
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$event->elements[2]->schedule_options);
		$this->assertEquals('As soon as possible',$event->elements[2]->schedule_options->name);
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

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect_3847238956934()
	{
		$resource = $this->getModifiedResource();

		$total_events = count(\Event::model()->findAll());
		$total_eo = count(\Element_OphTrOperationbooking_Operation::model()->findAll());
		$total_di = count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll());
		$total_sh = count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll());
		$total_b = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, $this->events('event6'));

		$this->assertEquals($total_events, count(\Event::model()->findAll()));
		$this->assertEquals($total_eo, count(\Element_OphTrOperationbooking_Operation::model()->findAll()));
		$this->assertEquals($total_di, count(\Element_OphTrOperationbooking_Diagnosis::model()->findAll()));
		$this->assertEquals($total_sh, count(\Element_OphTrOperationbooking_ScheduleOperation::model()->findAll()));
		$this->assertEquals($total_b-1, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, $this->events('event6'));

		$this->modified_event_assertions($resource, $event);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new EventService;
		$event = $ps->resourceToModel($resource, $this->events('event6'));
		$event = \Event::model()->findByPk($event->id);

		$this->modified_event_assertions($resource, $event);
	}

	public function modified_event_assertions($resource, $event)
	{
		$this->assertInstanceOf('Event',$event);
		$this->assertCount(3,$event->elements);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Diagnosis',$event->elements[0]);
		$this->assertEquals($resource->elements[0]->getId(),$event->elements[0]->id);
		$this->assertEquals(\Eye::model()->find('name=?',array('Left'))->id,$event->elements[0]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[0]->eye);
		$this->assertEquals('Left',$event->elements[0]->eye->name);
		$this->assertEquals(\Disorder::model()->find('term=?',array('Myocardial infarction'))->id,$event->elements[0]->disorder_id);
		$this->assertInstanceOf('Disorder',$event->elements[0]->disorder);
		$this->assertEquals('Myocardial infarction',$event->elements[0]->disorder->term);

		$this->assertInstanceOf('Element_OphTrOperationbooking_Operation',$event->elements[1]);
		$this->assertEquals($resource->elements[1]->getId(),$event->elements[1]->id);
		$this->assertEquals(\Eye::model()->find('name=?',array('Right'))->id,$event->elements[1]->eye_id);
		$this->assertInstanceOf('Eye',$event->elements[1]->eye);
		$this->assertEquals('Right',$event->elements[1]->eye->name);
		$this->assertEquals(0,$event->elements[1]->consultant_required);
		$this->assertEquals(\AnaestheticType::model()->find('name=?',array('Topical'))->id,$event->elements[1]->anaesthetic_type_id);
		$this->assertInstanceOf('AnaestheticType',$event->elements[1]->anaesthetic_type);
		$this->assertEquals('Topical',$event->elements[1]->anaesthetic_type->name);
		$this->assertEquals(1,$event->elements[1]->overnight_stay);
		$this->assertEquals(1,$event->elements[1]->site_id);
		$this->assertInstanceOf('Site',$event->elements[1]->site);
		$this->assertEquals(1,$event->elements[1]->site->id);
		$this->assertEquals(\OphTrOperationbooking_Operation_Priority::model()->find('name=?',array('Urgent'))->id,$event->elements[1]->priority_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$event->elements[1]->priority);
		$this->assertEquals('Urgent',$event->elements[1]->priority->name);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->decision_date);
		$this->assertEquals('i am testing',$event->elements[1]->comments);
		$this->assertEquals(60,$event->elements[1]->total_duration);
		$this->assertEquals(\OphTrOperationbooking_Operation_Status::model()->find('name=?',array('Rescheduled'))->id,$event->elements[1]->status_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Status',$event->elements[1]->status);
		$this->assertEquals('Rescheduled',$event->elements[1]->status->name);
		$this->assertEquals(0,$event->elements[1]->anaesthetist_required);
		$this->assertNull($event->elements[1]->operation_cancellation_date);
		$this->assertNull($event->elements[1]->cancellation_user);
		$this->assertNull($event->elements[1]->cancellation_reason);
		$this->assertEquals('',$event->elements[1]->cancellation_comment);
		$this->assertEquals(2,$event->elements[1]->latest_booking_id);
		$this->assertEquals('more RTT comments',$event->elements[1]->comments_rtt);
		$this->assertInstanceOf('Referral',$event->elements[1]->referral);
		$this->assertEquals(5,$event->elements[1]->referral->id);
		$this->assertInstanceOf('RTT',$event->elements[1]->rtt);
		$this->assertEquals(1,$event->elements[1]->rtt->id);

		$this->assertCount(2,$event->elements[1]->procedure_assignment);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Procedures',$event->elements[1]->procedure_assignment[0]);
		$this->assertEquals(\Procedure::model()->find('term=?',array('Foobar Procedure'))->id,$event->elements[1]->procedure_assignment[0]->proc_id);
		$this->assertEquals('Foobar Procedure',$event->elements[1]->procedure_assignment[0]->proc->term);
		$this->assertEquals(0,$event->elements[1]->procedure_assignment[0]->display_order);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Procedures',$event->elements[1]->procedure_assignment[1]);
		$this->assertEquals(\Procedure::model()->find('term=?',array('Test Procedure'))->id,$event->elements[1]->procedure_assignment[1]->proc_id);
		$this->assertEquals('Test Procedure',$event->elements[1]->procedure_assignment[1]->proc->term);
		$this->assertEquals(0,$event->elements[1]->procedure_assignment[1]->display_order);

		$this->assertCount(1,$event->elements[1]->allBookings);

		$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$event->elements[1]->allBookings[0]);
		$this->assertEquals($resource->elements[1]->allBookings[0]->getId(),$event->elements[1]->allBookings[0]->id);
		$this->assertEquals(10,$event->elements[1]->allBookings[0]->session_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$event->elements[1]->allBookings[0]->session);
		$this->assertEquals(10,$event->elements[1]->allBookings[0]->session->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->display_order);
		$this->assertEquals(2,$event->elements[1]->allBookings[0]->ward_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$event->elements[1]->allBookings[0]->ward);
		$this->assertEquals(2,$event->elements[1]->allBookings[0]->ward->id);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->admission_time);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->confirmed);
		$this->assertRegExp('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$event->elements[1]->allBookings[0]->session_date);
		$this->assertEquals('08:00:00',$event->elements[1]->allBookings[0]->session_start_time);
		$this->assertEquals('13:00:00',$event->elements[1]->allBookings[0]->session_end_time);
		$this->assertEquals(2,$event->elements[1]->allBookings[0]->session_theatre_id);
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$event->elements[1]->allBookings[0]->session_theatre);
		$this->assertEquals(2,$event->elements[1]->allBookings[0]->session_theatre->id);
		$this->assertEquals(1,$event->elements[1]->allBookings[0]->transport_arranged);
		$this->assertEquals('2013-04-04',$event->elements[1]->allBookings[0]->transport_arranged_date);
		$this->assertNull($event->elements[1]->allBookings[0]->booking_cancellation_date);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellationReason);
		$this->assertEquals('',$event->elements[1]->allBookings[0]->cancellation_comment);
		$this->assertNull($event->elements[1]->allBookings[0]->cancellation_user_id);

		$this->assertInstanceOf('Element_OphTrOperationbooking_ScheduleOperation',$event->elements[2]);
		$this->assertEquals($resource->elements[2]->getId(),$event->elements[2]->id);
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$event->elements[2]->schedule_options);
		$this->assertEquals('As soon as possible',$event->elements[2]->schedule_options->name);
	}

/*
	public function testJsonToResource()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago","given_name":"Yuri","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"2"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$ps = new PatientAssociatedContactsService;
		$resource = $ps->jsonToResource($json);

		$this->assertInstanceOf('services\PatientAssociatedContacts',$resource);
		$this->assertCount(3,$resource->contacts);

		$this->assertInstanceOf('services\PatientAssociatedContact',$resource->contacts[0]);
		$this->assertEquals('Dr',$resource->contacts[0]->title);
		$this->assertEquals('Zhivago',$resource->contacts[0]->family_name);
		$this->assertEquals('Yuri',$resource->contacts[0]->given_name);
		$this->assertEquals('999',$resource->contacts[0]->primary_phone);
		$this->assertNull($resource->contacts[0]->site_ref);
		$this->assertNull($resource->contacts[0]->institution_ref);

		$this->assertInstanceOf('services\PatientAssociatedContact',$resource->contacts[1]);
		$this->assertEquals('Mr',$resource->contacts[1]->title);
		$this->assertEquals('Inc',$resource->contacts[1]->family_name);
		$this->assertEquals('Apple',$resource->contacts[1]->given_name);
		$this->assertEquals('01010101',$resource->contacts[1]->primary_phone);
		$this->assertNull($resource->contacts[1]->institution_ref);
		$this->assertInstanceOf('services\SiteReference',$resource->contacts[1]->site_ref);
		$this->assertEquals(2,$resource->contacts[1]->site_ref->getId());

		$this->assertInstanceOf('services\PatientAssociatedContact',$resource->contacts[2]);
		$this->assertEquals('Ti',$resource->contacts[2]->title);
		$this->assertEquals('Tiss',$resource->contacts[2]->family_name);
		$this->assertEquals('Prac',$resource->contacts[2]->given_name);
		$this->assertEquals('0303032332',$resource->contacts[2]->primary_phone);
		$this->assertNull($resource->contacts[2]->site_ref);
		$this->assertInstanceOf('services\InstitutionReference',$resource->contacts[2]->institution_ref);
		$this->assertEquals(2,$resource->contacts[2]->institution_ref->getId());
	}

	public function testJsonToModel_NoSave_NoNewRows()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago","given_name":"Yuri","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"2"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$total_patients = count(\Patient::model()->findAll());
		$total_contacts = count(\Contact::model()->findAll());
		$total_pcas = count(\PatientContactAssignment::model()->findAll());
		$total_sites = count(\Site::model()->findAll());
		$total_institutions = count(\Institution::model()->findAll());

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, new \Patient, false);

		$this->assertEquals($total_patients, count(\Patient::model()->findAll()));
		$this->assertEquals($total_contacts, count(\Contact::model()->findAll()));
		$this->assertEquals($total_pcas, count(\PatientContactAssignment::model()->findAll()));
		$this->assertEquals($total_sites, count(\Site::model()->findAll()));
		$this->assertEquals($total_institutions, count(\Institution::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago","given_name":"Yuri","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"2"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, new \Patient, false);

		$this->assertInstanceOf('Patient',$patient);
		$this->assertCount(3,$patient->contactAssignments);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[0]);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[0]->contact);
		$this->assertEquals('Dr',$patient->contactAssignments[0]->contact->title);
		$this->assertEquals('Zhivago',$patient->contactAssignments[0]->contact->last_name);
		$this->assertEquals('Yuri',$patient->contactAssignments[0]->contact->first_name);
		$this->assertEquals('999',$patient->contactAssignments[0]->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[0]->location);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[1]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[1]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[1]->location->contact);
		$this->assertEquals('Mr',$patient->contactAssignments[1]->location->contact->title);
		$this->assertEquals('Inc',$patient->contactAssignments[1]->location->contact->last_name);
		$this->assertEquals('Apple',$patient->contactAssignments[1]->location->contact->first_name);
		$this->assertEquals('01010101',$patient->contactAssignments[1]->location->contact->primary_phone);
		$this->assertEquals(2,$patient->contactAssignments[1]->location->site_id);
		$this->assertNull($patient->contactAssignments[1]->location->institution_id);
		$this->assertNull($patient->contactAssignments[1]->contact);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[2]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[2]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[2]->location->contact);
		$this->assertEquals('Ti',$patient->contactAssignments[2]->location->contact->title);
		$this->assertEquals('Tiss',$patient->contactAssignments[2]->location->contact->last_name);
		$this->assertEquals('Prac',$patient->contactAssignments[2]->location->contact->first_name);
		$this->assertEquals('0303032332',$patient->contactAssignments[2]->location->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[2]->location->site_id);
		$this->assertEquals(2,$patient->contactAssignments[2]->location->institution_id);
		$this->assertNull($patient->contactAssignments[2]->contact);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago2","given_name":"Yuri2","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"1"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$total_patients = count(\Patient::model()->findAll());
		$total_contacts = count(\Contact::model()->findAll());
		$total_pcas = count(\PatientContactAssignment::model()->findAll());
		$total_sites = count(\Site::model()->findAll());
		$total_institutions = count(\Institution::model()->findAll());

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, $this->patients('patient4'));
		$patient = \Patient::model()->findByPk($patient->id);

		$this->assertEquals($total_patients, count(\Patient::model()->findAll()));
		$this->assertEquals($total_contacts+3, count(\Contact::model()->findAll()));
		$this->assertEquals($total_pcas+3, count(\PatientContactAssignment::model()->findAll()));
		$this->assertEquals($total_sites, count(\Site::model()->findAll()));
		$this->assertEquals($total_institutions, count(\Institution::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago2","given_name":"Yuri2","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"1"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, $this->patients('patient4'));
		$patient = \Patient::model()->findByPk($patient->id);

		$this->assertInstanceOf('Patient',$patient);
		$this->assertCount(3,$patient->contactAssignments);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[0]);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[0]->contact);
		$this->assertEquals('Dr',$patient->contactAssignments[0]->contact->title);
		$this->assertEquals('Zhivago2',$patient->contactAssignments[0]->contact->last_name);
		$this->assertEquals('Yuri2',$patient->contactAssignments[0]->contact->first_name);
		$this->assertEquals('999',$patient->contactAssignments[0]->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[0]->location);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[1]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[1]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[1]->location->contact);
		$this->assertEquals('Mr',$patient->contactAssignments[1]->location->contact->title);
		$this->assertEquals('Inc',$patient->contactAssignments[1]->location->contact->last_name);
		$this->assertEquals('Apple',$patient->contactAssignments[1]->location->contact->first_name);
		$this->assertEquals('01010101',$patient->contactAssignments[1]->location->contact->primary_phone);
		$this->assertEquals(2,$patient->contactAssignments[1]->location->site_id);
		$this->assertNull($patient->contactAssignments[1]->location->institution_id);
		$this->assertNull($patient->contactAssignments[1]->contact);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[2]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[2]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[2]->location->contact);
		$this->assertEquals('Ti',$patient->contactAssignments[2]->location->contact->title);
		$this->assertEquals('Tiss',$patient->contactAssignments[2]->location->contact->last_name);
		$this->assertEquals('Prac',$patient->contactAssignments[2]->location->contact->first_name);
		$this->assertEquals('0303032332',$patient->contactAssignments[2]->location->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[2]->location->site_id);
		$this->assertEquals(1,$patient->contactAssignments[2]->location->institution_id);
		$this->assertNull($patient->contactAssignments[2]->contact);
	}

	public function testJsonToModel_Save_Update_ModelCountsCorrect()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago","given_name":"Yuri","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"1"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$total_patients = count(\Patient::model()->findAll());
		$total_contacts = count(\Contact::model()->findAll());
		$total_pcas = count(\PatientContactAssignment::model()->findAll());
		$total_sites = count(\Site::model()->findAll());
		$total_institutions = count(\Institution::model()->findAll());

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, $this->patients('patient1'));
		$patient = \Patient::model()->findByPk($patient->id);

		$this->assertEquals($total_patients, count(\Patient::model()->findAll()));
		$this->assertEquals($total_contacts+2, count(\Contact::model()->findAll()));
		$this->assertEquals($total_pcas, count(\PatientContactAssignment::model()->findAll()));
		$this->assertEquals($total_sites, count(\Site::model()->findAll()));
		$this->assertEquals($total_institutions, count(\Institution::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$json = '{"contacts":[{"title":"Dr","family_name":"Zhivago","given_name":"Yuri","primary_phone":"999","institution_ref":null,"site_ref":null,"id":null,"last_modified":null},{"title":"Mr","family_name":"Inc","given_name":"Apple","primary_phone":"01010101","institution_ref":null,"site_ref":{"service":"Site","id":"2"},"id":null,"last_modified":null},{"title":"Ti","family_name":"Tiss","given_name":"Prac","primary_phone":"0303032332","institution_ref":{"service":"Institution","id":"1"},"site_ref":null,"id":null,"last_modified":null}],"id":null,"last_modified":null,"patient_id":{"id":"3","last_modified":-2208988800}}';

		$ps = new PatientAssociatedContactsService;
		$patient = $ps->jsonToModel($json, $this->patients('patient1'));
		$patient = \Patient::model()->findByPk($patient->id);

		$this->assertInstanceOf('Patient',$patient);
		$this->assertCount(3,$patient->contactAssignments);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[0]);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[0]->contact);
		$this->assertEquals('Dr',$patient->contactAssignments[0]->contact->title);
		$this->assertEquals('Zhivago',$patient->contactAssignments[0]->contact->last_name);
		$this->assertEquals('Yuri',$patient->contactAssignments[0]->contact->first_name);
		$this->assertEquals('999',$patient->contactAssignments[0]->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[0]->location);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[1]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[1]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[1]->location->contact);
		$this->assertEquals('Mr',$patient->contactAssignments[1]->location->contact->title);
		$this->assertEquals('Inc',$patient->contactAssignments[1]->location->contact->last_name);
		$this->assertEquals('Apple',$patient->contactAssignments[1]->location->contact->first_name);
		$this->assertEquals('01010101',$patient->contactAssignments[1]->location->contact->primary_phone);
		$this->assertEquals(2,$patient->contactAssignments[1]->location->site_id);
		$this->assertNull($patient->contactAssignments[1]->location->institution_id);
		$this->assertNull($patient->contactAssignments[1]->contact);

		$this->assertInstanceOf('PatientContactAssignment',$patient->contactAssignments[2]);
		$this->assertInstanceOf('ContactLocation',$patient->contactAssignments[2]->location);
		$this->assertInstanceOf('Contact',$patient->contactAssignments[2]->location->contact);
		$this->assertEquals('Ti',$patient->contactAssignments[2]->location->contact->title);
		$this->assertEquals('Tiss',$patient->contactAssignments[2]->location->contact->last_name);
		$this->assertEquals('Prac',$patient->contactAssignments[2]->location->contact->first_name);
		$this->assertEquals('0303032332',$patient->contactAssignments[2]->location->contact->primary_phone);
		$this->assertNull($patient->contactAssignments[2]->location->site_id);
		$this->assertEquals(1,$patient->contactAssignments[2]->location->institution_id);
		$this->assertNull($patient->contactAssignments[2]->contact);
	}
	*/
}
