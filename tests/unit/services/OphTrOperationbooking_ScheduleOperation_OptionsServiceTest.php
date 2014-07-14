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

class OphTrOperationbooking_ScheduleOperation_OptionsServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'options' => 'OphTrOperationbooking_ScheduleOperation_Options',
	);

	public function testModelToResource()
	{
		$option = $this->options('so1');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;

		$resource = $ps->modelToResource($option);

		$this->verifyResource($resource, $option);
	}

	public function verifyResource($resource, $option)
	{
		foreach (array('name','display_order','active') as $field) {
			$this->assertEquals($this->options('so1')->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_ScheduleOperation_Options(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_Options, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_Options, false);

		$this->verifyOption($option, $resource);
	}

	public function verifyOption($option, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$option);

		foreach (array('name','display_order','active') as $field) {
			$this->assertEquals($resource->$field,$option->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->name = 'wabwabwab';
		$resource->display_order = '93';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_Options);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
	}

	public function verifyNewOption($option)
	{
		$this->assertInstanceOf('OphTrOperationbooking_ScheduleOperation_Options',$option);

		$this->assertEquals('wabwabwab',$option->name);
		$this->assertEquals(93,$option->display_order);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_Options);

		$this->verifyNewOption($option);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, new \OphTrOperationbooking_ScheduleOperation_Options);
		$option = \OphTrOperationbooking_ScheduleOperation_Options::model()->findByPk($option->id);

		$this->verifyNewOption($option);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, $this->options('so1'));

		$this->assertEquals($ts, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, $this->options('so1'));

		$this->assertEquals(1,$option->id);

		$this->verifyNewOption($option);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->resourceToModel($resource, $this->options('so1'));
		$option = \OphTrOperationbooking_ScheduleOperation_Options::model()->findByPk($option->id);

		$this->assertEquals(1,$option->id);

		$this->verifyNewOption($option);
	}

	public function testJsonToResource()
	{
		$option = $this->options('so1');
		$json = \Yii::app()->service->OphTrOperationbooking_ScheduleOperation_Options($option->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $option);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_Options, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_Options, false);

		$this->verifyOption($option, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_Options);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_Options);

		$this->verifyNewOption($option);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, new \OphTrOperationbooking_ScheduleOperation_Options);
		$option = \OphTrOperationbooking_ScheduleOperation_Options::model()->findByPk($option->id);

		$this->verifyNewOption($option);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, $this->options('so1'));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_ScheduleOperation_Options::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, $this->options('so1'));

		$this->assertEquals(1,$option->id);

		$this->verifyNewOption($option);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_ScheduleOperation_OptionsService;
		$option = $ps->jsonToModel($json, $this->options('so1'));
		$option = \OphTrOperationbooking_ScheduleOperation_Options::model()->findByPk($option->id);

		$this->assertEquals(1,$option->id);

		$this->verifyNewOption($option);
	}
}
