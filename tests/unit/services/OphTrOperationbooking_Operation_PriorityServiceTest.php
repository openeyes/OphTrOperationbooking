<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your priority) any later version.
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

class OphTrOperationbooking_Operation_PriorityServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'priorities' => 'OphTrOperationbooking_Operation_Priority',
	);

	public function testModelToResource()
	{
		$priority = $this->priorities(0);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;

		$resource = $ps->modelToResource($priority);

		$this->verifyResource($resource, $priority);
	}

	public function verifyResource($resource, $priority)
	{
		foreach (array('name','display_order') as $field) {
			$this->assertEquals($this->priorities(0)->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Priority(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Priority, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Priority, false);

		$this->verifyOption($priority, $resource);
	}

	public function verifyOption($priority, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$priority);

		foreach (array('name','display_order') as $field) {
			$this->assertEquals($resource->$field,$priority->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->name = 'wabwabwab';
		$resource->display_order = 121;

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Priority);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
	}

	public function verifyNewOption($priority)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Priority',$priority);

		$this->assertEquals('wabwabwab',$priority->name);
		$this->assertEquals(121,$priority->display_order);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Priority);

		$this->verifyNewOption($priority);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Priority);
		$priority = \OphTrOperationbooking_Operation_Priority::model()->findByPk($priority->id);

		$this->verifyNewOption($priority);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, $this->priorities(0));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, $this->priorities(0));

		$this->assertEquals(1,$priority->id);

		$this->verifyNewOption($priority);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->resourceToModel($resource, $this->priorities(0));
		$priority = \OphTrOperationbooking_Operation_Priority::model()->findByPk($priority->id);

		$this->assertEquals(1,$priority->id);

		$this->verifyNewOption($priority);
	}

	public function testJsonToResource()
	{
		$priority = $this->priorities(0);
		$json = \Yii::app()->service->OphTrOperationbooking_Operation_Priority($priority->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $priority);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Priority, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Priority, false);

		$this->verifyOption($priority, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Priority);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Priority);

		$this->verifyNewOption($priority);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Priority);
		$priority = \OphTrOperationbooking_Operation_Priority::model()->findByPk($priority->id);

		$this->verifyNewOption($priority);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Priority::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, $this->priorities(0));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Priority::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, $this->priorities(0));

		$this->assertEquals(1,$priority->id);

		$this->verifyNewOption($priority);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_PriorityService;
		$priority = $ps->jsonToModel($json, $this->priorities(0));
		$priority = \OphTrOperationbooking_Operation_Priority::model()->findByPk($priority->id);

		$this->assertEquals(1,$priority->id);

		$this->verifyNewOption($priority);
	}
}
