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

class OphTrOperationbooking_Waiting_List_Contact_RuleServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'rules' => 'OphTrOperationbooking_Waiting_List_Contact_Rule',
		'sites' => 'Site',
		'services' => 'Service',
		'firms' => 'Firm',
	);

	public function testModelToResource()
	{
		$rule = $this->rules(0);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;

		$resource = $ps->modelToResource($rule);

		$this->verifyResource($resource, $rule);
	}

	public function verifyResource($resource, $rule)
	{
		$this->assertEquals($rule->id,$resource->getId());

		$this->assertNull($resource->parent_rule_ref);

		$this->assertInstanceOf('services\SiteReference',$resource->site_ref);
		$this->assertEquals($rule->site_id,$resource->site_ref->getId());

		$this->assertInstanceOf('services\ServiceReference',$resource->service_ref);
		$this->assertEquals($rule->service_id,$resource->service_ref->getId());

		$this->assertInstanceOf('services\FirmReference',$resource->firm_ref);
		$this->assertEquals($rule->firm_id,$resource->firm_ref->getId());

		foreach (array('rule_order','is_child','name','telephone') as $field) {
			$this->assertEquals($this->rules(0)->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Waiting_List_Contact_Rule(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Waiting_List_Contact_Rule, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Waiting_List_Contact_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function verifyRule($rule, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Waiting_List_Contact_Rule',$rule);

		$this->assertEquals($resource->site_ref->getId(),$rule->site_id);
		$this->assertEquals($resource->service_ref->getId(),$rule->service_id);
		$this->assertEquals($resource->firm_ref->getId(),$rule->firm_id);

		foreach (array('rule_order','is_child','name','telephone') as $field) {
			$this->assertEquals($resource->$field,$rule->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->site_ref = \Yii::app()->service->Site(2);
		$resource->service_ref = \Yii::app()->service->Service(2);
		$resource->firm_ref = \Yii::app()->service->Firm(2);

		$resource->rule_order = 123;
		$resource->is_child = 1;
		$resource->name = 'blahblah';
		$resource->telephone = '1239801';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Waiting_List_Contact_Rule);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
	}

	public function verifyNewRule($rule)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Waiting_List_Contact_Rule',$rule);

		$this->assertEquals(2,$rule->site_id);
		$this->assertEquals(2,$rule->service_id);
		$this->assertEquals(2,$rule->firm_id);

		$this->assertEquals(123,$rule->rule_order);
		$this->assertEquals(1,$rule->is_child);
		$this->assertEquals('blahblah',$rule->name);
		$this->assertEquals('1239801',$rule->telephone);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Waiting_List_Contact_Rule);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Waiting_List_Contact_Rule);
		$rule = \OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));
		$rule = \OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToResource()
	{
		$rule = $this->rules(0);
		$json = \Yii::app()->service->OphTrOperationbooking_Waiting_List_Contact_Rule($rule->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $rule);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Waiting_List_Contact_Rule, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Waiting_List_Contact_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Waiting_List_Contact_Rule);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Waiting_List_Contact_Rule);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Waiting_List_Contact_Rule);
		$rule = \OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Waiting_List_Contact_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));
		$rule = \OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}
}
