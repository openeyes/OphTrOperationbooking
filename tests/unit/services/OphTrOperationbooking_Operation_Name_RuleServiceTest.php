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

class OphTrOperationbooking_Operation_Name_RuleServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'rules' => 'OphTrOperationbooking_Operation_Name_Rule',
	);

	public function testModelToResource()
	{
		$rule = $this->rules(0);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;

		$resource = $ps->modelToResource($rule);

		$this->verifyResource($resource, $rule);
	}

	public function verifyResource($resource, $rule)
	{
		$this->assertEquals($rule->id,$resource->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->theatre_ref);
		$this->assertEquals($rule->theatre_id,$resource->theatre_ref->getId());
		$this->assertEquals($rule->name,$resource->name);
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Name_Rule(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Name_Rule, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Name_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function verifyRule($rule, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Name_Rule',$rule);

		$this->assertEquals($resource->theatre_ref->getId(),$rule->theatre_id);
		$this->assertEquals($resource->name,$rule->name);
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->theatre_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(2);
		$resource->name = 'Comet';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Name_Rule);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
	}

	public function verifyNewRule($rule)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Name_Rule',$rule);

		$this->assertEquals(2,$rule->theatre_id);
		$this->assertEquals('Comet',$rule->name);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Name_Rule);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Name_Rule);
		$rule = \OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));
		$rule = \OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToResource()
	{
		$rule = $this->rules(0);
		$json = \Yii::app()->service->OphTrOperationbooking_Operation_Name_Rule($rule->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $rule);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Name_Rule, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Name_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Name_Rule);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Name_Rule);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Name_Rule);
		$rule = \OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Name_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Name_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));
		$rule = \OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}
}
