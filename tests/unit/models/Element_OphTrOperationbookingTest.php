<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

//TODO: rename this to Element_OphTrOperationbooking_OperationTest
class Element_OphTrOperationbookingTest extends CDbTestCase
{
	public $fixtures = array(
			'wards' => 'OphTrOperationbooking_Operation_Ward',
			'patients' => 'Patient',
			'referral_types' => 'ReferralType',
			'referrals' => 'Referral',
			'statuses' => 'OphTrOperationbooking_Operation_Status'
	);

	public static function setUpBeforeClass(){
		date_default_timezone_set('UTC');
		self::getFixtureManager()->basePath = Yii::getPathOfAlias( 'application.modules.ophtroperationbooking.tests.fixtures' );
	}

	static function tearDownAfterClass(){
		self::getFixtureManager()->basePath = Yii::getPathOfAlias( 'application.tests.fixtures' );
	}

	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * Checks both that the array values are equal, and that the keys for the array are in the same order
	 * which assertEquals does not appear to do.
	 *
	 * @param $expected
	 * @param $res
	 */
	protected function assertOrderedAssocArrayEqual($expected, $res)
	{
		$this->assertEquals($expected, $res);
		$this->assertEquals(array_keys($expected), array_keys($res), "Response key order does not match expected" . print_r($res, true));
	}

	protected function getMalePatient() {
		$p = ComponentStubGenerator::generate('Patient', array('gender' => 'M'));
		$p->expects( $this->any() )->method('isChild')->will($this->returnValue(false));
		return $p;
	}

	protected function getFemalePatient() {
		$p = ComponentStubGenerator::generate('Patient', array('gender' => 'F'));
		$p->expects( $this->any() )->method('isChild')->will($this->returnValue(false));
		return $p;
	}

	protected function getBoyPatient() {
		$p = ComponentStubGenerator::generate('Patient', array('gender' => 'M'));
		$p->expects( $this->any() )->method('isChild')->will($this->returnValue(true));
		return $p;
	}

	protected function getGirlPatient() {
		$p = ComponentStubGenerator::generate('Patient', array('gender' => 'F'));
		$p->expects( $this->any() )->method('isChild')->will($this->returnValue(true));
		return $p;
	}

	protected function getOperationForPatient($patient, $methods = null) {
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods($methods)
				->getMock();

		$op->event = ComponentStubGenerator::generate(
				'Event',
				array(
						'episode' => ComponentStubGenerator::generate(
										'Episode',
										array('patient' => $patient, 'patient_id' => $patient->id)
								)
				));
		return $op;
	}

	protected function getSessionForTheatre($theatre) {
		$dt = new DateTime();
		$session = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Session',
			array(
				'id' => 1,
				'theatre' => $theatre,
				'date' => $dt,
			));
		return $session;
	}

	public function testgetWardOptions_MaleAdult()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
			array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$op = $this->getOperationForPatient($this->getMalePatient());
		$res = $op->getWardOptions($session);

		$expected = array(
				$this->wards('ward1')->id => $this->wards('ward1')->name,
				$this->wards('ward4')->id => $this->wards('ward4')->name);

		$this->assertOrderedAssocArrayEqual($expected,$res);
	}

	public function testgetWardOptions_FemaleAdult()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$op = $this->getOperationForPatient($this->getFemalePatient());
		$res = $op->getWardOptions($session);

		$expected = array(
				$this->wards('ward2')->id => $this->wards('ward2')->name,
				$this->wards('ward4')->id => $this->wards('ward4')->name);

		$this->assertOrderedAssocArrayEqual($expected,$res);
	}

	public function testgetWardOptions_Boy()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$op = $this->getOperationForPatient($this->getBoyPatient());

		$res = $op->getWardOptions($session);

		$expected = array(
				$this->wards('ward5')->id => $this->wards('ward5')->name,
				$this->wards('ward6')->id => $this->wards('ward6')->name);

		$this->assertOrderedAssocArrayEqual($expected,$res);
	}

	public function testgetWardOptions_Girl()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$op = $this->getOperationForPatient($this->getGirlPatient());

		$res = $op->getWardOptions($session);

		$expected = array(
				$this->wards('ward6')->id => $this->wards('ward6')->name,
				$this->wards('ward3')->id => $this->wards('ward3')->name,
				);
		$this->assertOrderedAssocArrayEqual($expected,$res);
	}

	public function testgetWardOptions_OtherSite()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 2));
		$session = $this->getSessionForTheatre($theatre);

		$op = $this->getOperationForPatient($this->getMalePatient());

		$res = $op->getWardOptions($session);

		$expected = array(
				$this->wards('ward7')->id => $this->wards('ward7')->name,
		);
		$this->assertOrderedAssocArrayEqual($expected,$res);
	}

	public function testCantScheduleOperationWhenPatientUnavailable()
	{
		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$booking = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Booking', array('session' => $session));

		$op = $this->getOperationForPatient($this->getMalePatient());
		$op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
				->disableOriginalConstructor()
				->setMethods(array('isPatientAvailable'))
				->getMock();
		$op_opts->expects($this->once())
			->method('isPatientAvailable')
			->will($this->returnValue(false));

		$res = $op->schedule($booking, '', '', '', false, null, $op_opts);
		$this->assertFalse($res === true);
		# arrays are error messages
		$this->assertTrue(gettype($res) == 'array');
	}

	public function testProcedureCountSingleEye()
	{
		$op = new Element_OphTrOperationbooking_Operation();
		$op->procedures = array(new Procedure(), new Procedure());

		$this->assertEquals($op->getProcedureCount(), 2);
	}

	public function testProcedureCountBothEyes()
	{
		$op = new Element_OphTrOperationbooking_Operation();
		$op->procedures = array(new Procedure(), new Procedure());
		$op->eye_id = Eye::BOTH;

		$this->assertEquals($op->getProcedureCount(), 4);
	}

	public function testSchedule_ReferralRequiredWhenConfigured()
	{
		$curr = Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'];
		Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = true;

		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$booking = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_booking', array('session' => $session));

		$op = $this->getOperationForPatient($this->getMalePatient());
		$op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
				->disableOriginalConstructor()
				->setMethods(array('isPatientAvailable'))
				->getMock();

		$op->referral = null;
		$res = $op->schedule($booking, '', '', '', false, null, $op_opts);
		$this->assertFalse($res === true);
		# arrays are error messages
		$this->assertTrue(gettype($res) == 'array');
		$this->assertEquals("Referral required to schedule operation", $res[0][0]);

		Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = $curr;

	}

	public function testSchedule_ReferralNotRequiredWhenConfigured()
	{
		$curr = Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'];
		Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = false;
		$urgent = Yii::app()->params['urgent_booking_notify_hours'];
		Yii::app()->params['urgent_booking_notify_hours'] = false;

		// a lot of mocking needed as there's a lot of functionality in the schedule method
		// ... it might be nice to optimise this into a couple of different methods ...
		$booking = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
				->disableOriginalConstructor()
				->setMethods(array('save', 'audit'))
				->getMock();

		$booking->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$booking->expects($this->once())
			->method('audit');

		$theatre = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre',
				array('site_id' => 1));
		$session = $this->getSessionForTheatre($theatre);

		$session->expects($this->once())
			->method('operationBookable')
			->will($this->returnValue(true));

		// saved for comments
		$session->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$booking->session = $session;

		$op = $this->getOperationForPatient($this->getMalePatient(), array('save', 'calculateEROD'));
		$op->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$op->event->episode->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
				->disableOriginalConstructor()
				->setMethods(array('isPatientAvailable'))
				->getMock();

		$op_opts->expects($this->once())
			->method('isPatientAvailable')
			->will($this->returnValue(true));

		$session->expects($this->once())
			->method('isBookable')
			->will($this->returnValue(true));

		$op->referral = null;
		$res = $op->schedule($booking, '', '', '', false, null, $op_opts);
		$this->assertTrue($res === true);

		// reset params
		Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = $curr;
		Yii::app()->params['urgent_booking_notify_hours'] = $urgent;
	}

	public function testReferralValidatorMustBeCalled()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('validateReferral'));
		$op->referral_id = 1;
		$op->expects($this->once())
			->method('validateReferral')
			->with($this->equalTo('referral_id'), $this->isType('array'));

		$op->validate();
	}

	public function testReferralMustBelongtoPatient()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('addError'));

		$op->referral_id = $this->referrals('referral2')->id;

		$op->expects($this->once())
			->method('addError')
			->with($this->equalTo('referral_id'), $this->equalTo('Referral must be for the patient of the event'));

		$op->validateReferral('referral_id', array());
	}

	public function testWillStoreHasBookingsState()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('__get'));
		// although we don't care about the order, I don't think there's a way to expect
		// different calls to the same method in an arbitary order
		$op->expects($this->at(0))
			->method('__get')
			->with($this->equalTo('allBookings'))
			->will($this->returnValue(array(new OphTrOperationbooking_Operation_Booking())));

		$op->expects($this->at(1))
				->method('__get')
				->with($this->equalTo('referral_id'))
				->will($this->returnValue(1));

		$op->afterFind();
		// TODO: expand this to check storing original referral id as well
		$r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
		$hb_prop = $r->getProperty('_has_bookings');
		$hb_prop->setAccessible(true);
		$this->assertTrue($hb_prop->getValue($op));
		$ref_prop = $r->getProperty('_original_referral_id');
		$ref_prop->setAccessible(true);
		$this->assertEquals(1,$ref_prop->getValue($op));
	}

	public function testcanChangeReferral_true()
	{
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();
		$r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
		$hb_prop = $r->getProperty('_has_bookings');
		$hb_prop->setAccessible(true);
		$hb_prop->setValue($op, false);

		$this->assertTrue($op->canChangeReferral());
	}

	public function testvalidateReferral_CannotBeChangedAfterOperationScheduled()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('canChangeReferral','addError'));

		$op->expects($this->once())
			->method('canChangeReferral')
			->will($this->returnValue(false));

		$r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
		$ref_prop = $r->getProperty('_original_referral_id');
		$ref_prop->setAccessible(true);
		$ref_prop->setValue($op,5);

		$op->referral_id = $this->referrals('referral1')->id;

		$op->expects($this->once())
			->method('addError')
			->with($this->equalTo('referral_id'), 'Referral cannot be changed after an operation has been scheduled');

		$op->validateReferral('referral_id', array());
	}

	public function testsetStatus_noSave()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

		$op->expects($this->never())
			->method('save');

		$op->setStatus($this->statuses('scheduled')->name, false);
		$this->assertEquals($this->statuses('scheduled')->id, $op->status_id);
	}

	public function testsetStatus_save()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

		$op->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$op->setStatus($this->statuses('scheduled')->name);
		$this->assertEquals($this->statuses('scheduled')->id, $op->status_id);
	}

	public function testsetStatus_invalidStatus()
	{
		$op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

		$op->expects($this->never())
				->method('save');

		$this->setExpectedException('Exception', 'Invalid status: Invalid Test Status');
		$op->setStatus('Invalid Test Status');
	}



}