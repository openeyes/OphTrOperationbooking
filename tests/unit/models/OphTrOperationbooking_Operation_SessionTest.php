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

class OphTrOperationbooking_Operation_SessionTest  extends CDbTestCase
{
	public $fixtures = array(
		'theatres' =>  'OphTrOperationbooking_Operation_Theatre',
		'seq' => 'OphTrOperationbooking_Operation_Sequence',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
	);

	static public function setupBeforeClass()
	{
		Yii::import('application.modules.OphTrOperationbooking.helpers.*');
	}

	public function testUnavailableReasonRequired()
	{
		$test = new OphTrOperationbooking_Operation_Session();
		$basic_attrs = array(
			'sequence_id' => 1,
			'date' => '2014-04-03',
			'start_time' => '08:30',
			'end_time' => '13:30',
			'theatre_id' => $this->theatres('th1')->id,
		);

		$test->attributes = $basic_attrs;

		$test->available = false;
		$this->assertFalse($test->validate());
		$errs = $test->getErrors();
		$this->assertArrayHasKey("unavailablereason_id", $errs);

	}

	public function testOperationBookableSessionUnavailable()
	{
		$test = new OphTrOperationbooking_Operation_Session();
		$test->available = false;
		$op = new Element_OphTrOperationbooking_Operation();
		$this->assertFalse($test->operationBookable($op));
	}

	public function testUnbookableReasonUnavailableNoReason()
	{
		$test = new OphTrOperationbooking_Operation_Session();
		$test->available = false;
		$op = new Element_OphTrOperationbooking_Operation();
		$this->assertEquals($test->unbookableReason($op), OphTrOperationbooking_Operation_Session::$DEFAULT_UNAVAILABLE_REASON);
	}

	public function testUnbookableReasonUnavailableReason()
	{
		$test = new OphTrOperationbooking_Operation_Session();
		$reason = new OphTrOperationbooking_Operation_Session_UnavailableReason();
		$reason->name = "Test Reason";
		$test->available = false;
		$test->unavailablereason = $reason;
		$op = new Element_OphTrOperationbooking_Operation();

		$this->assertEquals($test->unbookableReason($op), OphTrOperationbooking_Operation_Session::$DEFAULT_UNAVAILABLE_REASON . ": " . $reason->name);
	}

	public function testCurrentProcedureCount()
	{
		$total_proc = 0;
		$bookings = array();
		$proc_counts = array(2,5,1);
		foreach ($proc_counts as $ct) {
			$booking = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
					->disableOriginalConstructor()
					->setMethods(array('getProcedureCount'))
					->getMock();
			$booking->expects($this->once())
				->method('getProcedureCount')
				->will($this->returnValue($ct));
			$bookings[] = $booking;
			$total_proc+=$ct;
		}


		$test = new OphTrOperationbooking_Operation_Session();
		$test->activeBookings = $bookings;

		$this->assertEquals($test->getBookedProcedureCount(), $total_proc);
	}

	public function testAvailableProcedureCountNoMax()
	{
		$test = new OphTrOperationbooking_Operation_Session();
		$this->assertNull($test->getAvailableProcedureCount());
	}

	public function testAvailableProcedureCount()
	{
		$test = $this->getMockBuilder('OphTrOperationbooking_Operation_Session')
				->disableOriginalConstructor()
				->setMethods(array('getBookedProcedureCount'))
				->getMock();
		$test->expects($this->once())
			->method('getBookedProcedureCount')
			->will($this->returnValue(2));

		$test->max_procedures = 5;

		$this->assertEquals($test->getAvailableProcedureCount(), 3);
	}

	public function testOperationBookableTooManyProcedures()
	{
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods(array('getProcedureCount'))
				->getMock();

		$op->expects($this->once())
			->method('getProcedureCount')
			->will($this->returnValue(5));

		$test = $this->getMockBuilder('OphTrOperationbooking_Operation_Session')
				->disableOriginalConstructor()
				->setMethods(array('getBookedProcedureCount'))
				->getMock();

		$test->expects($this->once())
			->method('getBookedProcedureCount')
			->will($this->returnValue(0));

		$test->max_procedures = 4;
		$test->available = true;

		$this->assertFalse($test->operationBookable($op));
	}

	public function testUnbookableReasonTooManyProcedures()
	{
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods(array('getProcedureCount'))
				->getMock();

		$op->expects($this->once())
				->method('getProcedureCount')
				->will($this->returnValue(5));

		$test = $this->getMockBuilder('OphTrOperationbooking_Operation_Session')
				->disableOriginalConstructor()
				->setMethods(array('getBookedProcedureCount'))
				->getMock();

		$test->expects($this->once())
				->method('getBookedProcedureCount')
				->will($this->returnValue(0));

		$test->max_procedures = 4;
		$test->available = true;

		$this->assertEquals($test->unbookableReason($op), OphTrOperationbooking_Operation_Session::$TOO_MANY_PROCEDURES_REASON);
	}

	public function testOperationBookableUnderProcedureLimit()
	{
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods(array('getProcedureCount'))
				->getMock();

		$op->expects($this->once())
				->method('getProcedureCount')
				->will($this->returnValue(5));

		$helper = $this->getMockBuilder('OphTrOperationbooking_BookingHelper')
				->disableOriginalConstructor()
				->setMethods(array('checkSessionCompatibleWithOperation'))
				->getMock();
		$helper->expects($this->once())
			->method('checkSessionCompatibleWithOperation')
			->will($this->returnValue(array()));

		$test = $this->getMockBuilder('OphTrOperationbooking_Operation_Session')
				->disableOriginalConstructor()
				->setMethods(array('getBookedProcedureCount', 'getHelper'))
				->getMock();

		$test->expects($this->once())
				->method('getBookedProcedureCount')
				->will($this->returnValue(0));
		$test->expects($this->once())
			->method('getHelper')
			->will($this->returnValue($helper));

		$test->max_procedures = 7;
		$test->available = true;
		$test->date = date('Y-m-d');

		$this->assertTrue($test->operationBookable($op));
	}

	public function testWarnings()
	{
		$test = $this->getMockBuilder('OphTrOperationbooking_Operation_Session')
				->disableOriginalConstructor()
				->setMethods(array('getAvailableProcedureCount','getAvailableMinutes'))
				->getMock();
		$test->expects($this->once())
			->method('getAvailableProcedureCount')
			->will($this->returnValue(-2));

		$test->expects($this->once())
				->method('getAvailableMinutes')
				->will($this->returnValue(-20));

		$warnings = $test->getWarnings();
		$this->assertCount(2, $warnings);
	}

	public function testMaxProceduresCannotBeLessThanExistingBookings()
	{
		$sess = new OphTrOperationbooking_Operation_Session;
		$sess->sequence_id = $this->seq('sequence1')->id;
		$sess->theatre_id = $this->theatres('th1')->id;
		$sess->date = date('Y-m-d');
		$sess->start_time = '10:00:00';
		$sess->end_time = '11:00:00';

		$sess->helper = $this->getMockBuilder('Ophtroperationbooking_Bookinghelper')->disableOriginalConstructor()->getMock();
		$sess->helper->expects($this->any())->method('checkSessionCompatibleWithOperation')->will($this->returnValue(array()));

		$bookings = array();
		for ($n = 1; $n <= 3; $n++) {
			$bookings[] = ComponentStubGenerator::generate(
				'OphTrOperationbooking_Operation_Booking',
				array(
					'operation' => ComponentStubGenerator::generate('Element_OphTrOperationbooking_Operation'),
					'procedureCount' => $n,
				)
			);
		}
		$sess->activeBookings = $bookings;

		$sess->max_procedures = 5;

		$this->assertFalse($sess->save());
		$this->assertEquals(
			array('max_procedures' => array('Max procedures cannot be lower than existing number of procedures booked into session')),
			$sess->getErrors()
		);
	}
}