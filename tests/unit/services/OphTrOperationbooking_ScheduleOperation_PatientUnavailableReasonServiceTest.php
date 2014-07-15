<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your reason) any later version.
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

class OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'reasons' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',
	);

	public function testModelToResource()
	{
		$reason = $this->reasons('reason1');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;

		$resource = $ps->modelToResource($reason);

		$this->verifyResource($resource, $reason);
	}

	public function verifyResource($resource, $reason)
	{
		foreach (array('name','display_order','enabled') as $field) {
			$this->assertEquals($this->reasons('reason1')->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason, false);

		$this->verifyOption($reason, $resource);
	}

	public function verifyOption($reason, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',$reason);

		foreach (array('name','display_order','enabled') as $field) {
			$this->assertEquals($resource->$field,$reason->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->name = 'wabwabwab';
		$resource->display_order = 123;

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
	}

	public function verifyNewOption($reason)
	{
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason',$reason);

		$this->assertEquals('wabwabwab',$reason->name);
		$this->assertEquals(123,$reason->display_order);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);

		$this->verifyNewOption($reason);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);
		$reason = \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($reason->id);

		$this->verifyNewOption($reason);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, $this->reasons('reason1'));

		$this->assertEquals($ts, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, $this->reasons('reason1'));

		$this->assertEquals(1,$reason->id);

		$this->verifyNewOption($reason);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->resourceToModel($resource, $this->reasons('reason1'));
		$reason = \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($reason->id);

		$this->assertEquals(1,$reason->id);

		$this->verifyNewOption($reason);
	}

	public function testJsonToResource()
	{
		$reason = $this->reasons('reason1');
		$json = \Yii::app()->service->OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason($reason->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $reason);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason, false);

		$this->verifyOption($reason, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);

		$this->verifyNewOption($reason);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason);
		$reason = \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($reason->id);

		$this->verifyNewOption($reason);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, $this->reasons('reason1'));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, $this->reasons('reason1'));

		$this->assertEquals(1,$reason->id);

		$this->verifyNewOption($reason);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_PatientUnavailableReasonService;
		$reason = $ps->jsonToModel($json, $this->reasons('reason1'));
		$reason = \OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($reason->id);

		$this->assertEquals(1,$reason->id);

		$this->verifyNewOption($reason);
	}
}
