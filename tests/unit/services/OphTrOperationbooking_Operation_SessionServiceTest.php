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

class OphTrOperationbooking_Operation_SessionServiceTest extends \CDbTestCase
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
		'sequences' => 'OphTrOperationbooking_Operation_Sequence',
		'sessions' => 'OphTrOperationbooking_Operation_Session',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
		'specialties' => 'Specialty',
		'subspecialties' => 'Subspecialty',
		'rtts' => 'RTT',
		'rf_type' => 'ReferralType',
		'rf' => 'Referral',
	);

	public function setup()
	{
		$path = preg_replace('/protected\/.*$/','/protected/',getcwd());
		require_once($path."modules/OphTrOperationbooking/helpers/OphTrOperationbooking_BookingHelper.php");

		parent::setup();
	}

	public function testModelToResource()
	{
		$session = $this->sessions('session5');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;

		$resource = $ps->modelToResource($session);

		$this->verifyResource($resource, $session);
	}

	public function verifyResource($resource, $session)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Session',$resource);
		$this->assertEquals($session->id,$resource->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceReference',$resource->sequence_ref);
		$this->assertEquals($session->sequence_id,$resource->sequence_ref->getId());

		$this->assertInstanceOf('services\FirmReference',$resource->firm_ref);
		$this->assertEquals($session->firm_id,$resource->firm_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->theatre_ref);
		$this->assertEquals($session->theatre_id,$resource->theatre_ref->getId());

		$this->assertInstanceOf('services\Date',$resource->date);

		foreach (array('start_time','end_time','consultant','paediatric','anaesthetist','general_anaesthetic','default_admission_time') as $field) {
			$this->assertEquals($session->$field,$resource->$field);
		}

		$this->assertCount(2,$resource->bookings);

		$this->assertInstanceOf('services\EventReference',$resource->bookings[0]);
		$this->assertEquals(1,$resource->bookings[0]->getId());

		$this->assertInstanceOf('services\EventReference',$resource->bookings[1]);
		$this->assertEquals(6,$resource->bookings[1]->getId());
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Session(5)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$total_s = count(\OphTrOperationbooking_Operation_Session::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Session, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Session, false);

		$this->verifySession($session, $resource);
	}

	public function verifySession($session, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$session);

		foreach ($keys as $key => $value) {
			if (is_null($value)) {
				$this->assertNull($session->$key);
			} else {
				$this->assertEquals($value, $session->$key);
			}
		}

		foreach ($session->getAttributes() as $key => $value) {
			if ($key != 'id' && !in_array($key,array_keys($keys)) && !in_array($key,array('created_date','last_modified_date'))) {
				$this->assertEquals($this->sessions('session5')->$key,$value);
			}
		}

		$this->assertCount(count($resource->bookings),$session->activeBookings);

		foreach ($resource->bookings as $i => $booking_event) {
			$this->assertInstanceOf('OphTrOperationbooking_Operation_Booking',$session->activeBookings[$i]);
			$this->assertEquals(\OphTrOperationbooking_Operation_Booking::model()->with(array(
					'operation' => array(
						'with' => 'event',
					)
				))
				->find('event.id=?',array($booking_event->getId()))->id,
				$session->activeBookings[$i]->id
			);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->sequence_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Sequence(2);
		$resource->firm_ref = \Yii::app()->service->Firm(2);
		$resource->date = new Date('2012-01-01');
		$resource->start_time = '13:30:00';
		$resource->end_time = '18:00:00';
		$resource->comments = 'testing 123';
		$resource->paediatric = 1;
		$resource->anaesthetist = 1;
		$resource->general_anaesthetic = 1;
		$resource->theatre_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(1);
		$resource->default_admission_time = '13:00:00';
		$resource->max_procedures = 20;

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Operation_Session::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Session);

		$this->assertEquals($ts+1, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function verifyNewSession($session)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Session',$session);

		$this->assertEquals(2,$session->sequence_id);
		$this->assertEquals(2,$session->firm_id);
		$this->assertEquals('2012-01-01',$session->date);
		$this->assertEquals('13:30:00',$session->start_time);
		$this->assertEquals('18:00:00',$session->end_time);
		$this->assertEquals('testing 123',$session->comments);
		$this->assertEquals(1,$session->paediatric);
		$this->assertEquals(1,$session->anaesthetist);
		$this->assertEquals(1,$session->general_anaesthetic);
		$this->assertEquals(1,$session->theatre_id);
		$this->assertEquals('13:00:00',$session->default_admission_time);
		$this->assertEquals(20,$session->max_procedures);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Session);

		$this->verifyNewSession($session, $resource, array(
			'start_time' => '13:30:00',
			'end_time' => '18:00:00',
		));

		$this->assertEquals(0,count($session->activeBookingEvents));
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Session);
		$session = \OphTrOperationbooking_Operation_Session::model()->findByPk($session->id);

		$this->verifyNewSession($session, $resource, array(
			'start_time' => '13:30:00',
			'end_time' => '18:00:00',
		));

		$this->assertEquals(0,count($session->activeBookingEvents));
	}

	public function getModifiedResource()
	{
		$resource = $this->getNewResource();

		$resource->bookings = array(
			\Yii::app()->service->OphTrOperationbooking_Event(6),
			\Yii::app()->service->OphTrOperationbooking_Event(1),
		);

		return $resource;
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ts = count(\OphTrOperationbooking_Operation_Session::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$sequence = $ps->resourceToModel($resource, $this->sessions('session5'));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function verifyModifiedSession($session)
	{
		$this->assertEquals($this->sessions('session5')->id, $session->id);

		$this->verifyNewSession($session);

		$this->assertEquals(6,$session->activeBookings[0]->operation->event_id);
		$this->assertEquals(1,$session->activeBookings[1]->operation->event_id);
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->resourceToModel($resource, $this->sessions('session5'));

		$this->assertEquals(5,$session->id);

		$this->verifyModifiedSession($session, $resource);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->resourceToModel($resource, $this->sessions('session5'));
		$session = \OphTrOperationbooking_Operation_Session::model()->findByPk($session->id);

		$this->assertEquals(5,$session->id);

		$this->verifyModifiedSession($session, $resource);
	}

	public function testJsonToResource()
	{
		$session = $this->sessions('session5');
		$json = \Yii::app()->service->OphTrOperationbooking_Operation_Session($session->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $session);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Session::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Session, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Session, false);

		$this->verifySession($session, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Session::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Session);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Session);

		$this->verifyNewSession($session, $resource, array(
			'start_time' => '13:30:00',
			'end_time' => '18:00:00',
		));

		$this->assertEquals(0,count($session->activeBookingEvents));
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Session);
		$session = \OphTrOperationbooking_Operation_Session::model()->findByPk($session->id);

		$this->verifyNewSession($session, $resource, array(
			'start_time' => '13:30:00',
			'end_time' => '18:00:00',
		));

		$this->assertEquals(0,count($session->activeBookingEvents));
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Session::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, $this->sessions('session5'));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Session::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, $this->sessions('session5'));

		$this->assertEquals(5,$session->id);

		$this->verifyModifiedSession($session);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SessionService;
		$session = $ps->jsonToModel($json, $this->sessions('session5'));
		$session = \OphTrOperationbooking_Operation_Session::model()->findByPk($session->id);

		$this->assertEquals(5,$session->id);

		$this->verifyModifiedSession($session);
	}
}
