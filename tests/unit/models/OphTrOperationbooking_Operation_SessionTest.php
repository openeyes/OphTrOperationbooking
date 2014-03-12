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
			'wards' => 'OphTrOperationbooking_Operation_Ward',
			'theatres' =>  'OphTrOperationbooking_Operation_Theatre'
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
}