<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your ruletype) any later version.
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

class OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'ruletypes' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule_Type',
	);

	public function testModelToResource()
	{
		$ruletype = $this->ruletypes(0);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;

		$resource = $ps->modelToResource($ruletype);

		$this->verifyResource($resource, $ruletype);
	}

	public function verifyResource($resource, $ruletype)
	{
		foreach (array('name') as $field) {
			$this->assertEquals($this->ruletypes(0)->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Admission_Letter_Warning_Rule_Type(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type, false);

		$this->verifyOption($ruletype, $resource);
	}

	public function verifyOption($ruletype, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Admission_Letter_Warning_Rule_Type',$ruletype);

		foreach (array('name') as $field) {
			$this->assertEquals($resource->$field,$ruletype->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->name = 'wabwabwab';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
	}

	public function verifyNewOption($ruletype)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Admission_Letter_Warning_Rule_Type',$ruletype);

		$this->assertEquals('wabwabwab',$ruletype->name);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);

		$this->verifyNewOption($ruletype);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);
		$ruletype = \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findByPk($ruletype->id);

		$this->verifyNewOption($ruletype);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, $this->ruletypes(0));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, $this->ruletypes(0));

		$this->assertEquals(1,$ruletype->id);

		$this->verifyNewOption($ruletype);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->resourceToModel($resource, $this->ruletypes(0));
		$ruletype = \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findByPk($ruletype->id);

		$this->assertEquals(1,$ruletype->id);

		$this->verifyNewOption($ruletype);
	}

	public function testJsonToResource()
	{
		$ruletype = $this->ruletypes(0);
		$json = \Yii::app()->service->OphTrOperationbooking_Admission_Letter_Warning_Rule_Type($ruletype->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $ruletype);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type, false);

		$this->verifyOption($ruletype, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);

		$this->verifyNewOption($ruletype);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type);
		$ruletype = \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findByPk($ruletype->id);

		$this->verifyNewOption($ruletype);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, $this->ruletypes(0));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, $this->ruletypes(0));

		$this->assertEquals(1,$ruletype->id);

		$this->verifyNewOption($ruletype);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_Rule_TypeService;
		$ruletype = $ps->jsonToModel($json, $this->ruletypes(0));
		$ruletype = \OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findByPk($ruletype->id);

		$this->assertEquals(1,$ruletype->id);

		$this->verifyNewOption($ruletype);
	}
}
