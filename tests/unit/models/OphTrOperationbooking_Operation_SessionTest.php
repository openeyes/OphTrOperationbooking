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

class OphTrOperationbooking_Operation_SessionTest extends CDbTestCase
{
	protected $fixtures = array(
		'booking' => 'Ophtroperationbooking_Operation_Booking',
		'erod' => 'Ophtroperationbooking_Operation_EROD',
		'op' => 'Element_Ophtroperationbooking_Operation',
		'session' => 'OphTrOperationbooking_Operation_Session',
	);

	protected function assertPreConditons()
	{
		$this->assertEquals($this->session('ses5')->id, $this->booking('b1')->session_id);
		$this->assertEquals($this->session('ses1')->id, $this->erod('erod1')->session_id);
	}

	public function testDissociateFromCancelledBookingOnDelete()
	{
		$booking = $this->booking('b1');
		$booking->booking_cancellation_date = date('Y-m-d');
		$booking->save();

		$this->session('ses5')->delete();

		$booking->refresh();
		$this->assertNull($booking->session_id);
	}

	/**
	 * @expectedException CDbException
	 * @expectedExceptionMessage constraint violation
	 */
	public function testDontDissociateFromNonCancelledBookingOnDelete()
	{
		$this->session('ses5')->delete();
	}

	public function testDissociateFromErodOnDelete()
	{
		$this->session('ses1')->delete();

		$erod = $this->erod('erod1');
		$erod->refresh();
		$this->assertNull($erod->session_id);
	}
}
