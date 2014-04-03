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

class Element_OphTrOperationbookingTest extends CDbTestCase
{
	public $fixtures = array(
			'wards' => 'OphTrOperationbooking_Operation_Ward',
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

	protected function getOperationForPatient($patient) {
		$op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$op->event = ComponentStubGenerator::generate(
				'Event',
				array(
						'episode' => ComponentStubGenerator::generate(
										'Episode',
										array('patient' => $patient)
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


}