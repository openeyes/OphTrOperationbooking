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

class OphTrOperationbooking_Admission_Letter_Warning_RuleServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'rules' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule',
		'sites' => 'Site',
		'theatres' => 'OphTrOperationbooking_Operation_Theatre',
		'subspecialties' => 'Subspecialty',
		'firms' => 'Firm',
	);

	public function testModelToResource()
	{
		$rule = $this->rules(0);

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;

		$resource = $ps->modelToResource($rule);

		$this->verifyResource($resource, $rule);
	}

	public function verifyResource($resource, $rule)
	{
		$this->assertEquals($rule->id,$resource->getId());

		if ($rule->parent_rule_id) {
			$this->assertEquals($rule->parent_rule_id,$resource->parent_rule_ref->getId());
		} else {
			$this->assertNull($resource->parent_rule_ref);
		}

		$this->assertInstanceOf('services\SiteReference',$resource->site_ref);
		$this->assertEquals($rule->site_id,$resource->site_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->theatre_ref);
		$this->assertEquals($rule->theatre_id,$resource->theatre_ref->getId());

		$this->assertInstanceOf('services\SubspecialtyReference',$resource->subspecialty_ref);
		$this->assertEquals($rule->subspecialty_id,$resource->subspecialty_ref->getId());

		$this->assertInstanceOf('services\FirmReference',$resource->firm_ref);
		$this->assertEquals($rule->firm_id,$resource->firm_ref->getId());

		foreach (array('rule_order','is_child','show_warning','warning_text','emphasis','strong') as $field) {
			$this->assertEquals($this->rules(0)->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Admission_Letter_Warning_Rule(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function verifyRule($rule, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Admission_Letter_Warning_Rule',$rule);

		foreach (array('rule_order','is_child','show_warning','warning_text','emphasis','strong') as $field) {
			$this->assertEquals($resource->$field,$rule->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->rule_type_ref = \Yii::app()->service->OphTrOperationbooking_Admission_Letter_Warning_Rule_Type(2);
		$resource->rule_order = 3;
		$resource->site_ref = \Yii::app()->service->Site(2);
		$resource->theatre_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(2);
		$resource->subspecialty_ref = \Yii::app()->service->Subspecialty(2);
		$resource->is_child = 1;
		$resource->show_warning = 0;
		$resource->warning_text = 'flimflam';
		$resource->emphasis = 0;
		$resource->strong = 0;
		$resource->firm_ref = \Yii::app()->service->Firm(2);

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
	}

	public function verifyNewRule($rule)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Admission_Letter_Warning_Rule',$rule);

		$this->assertEquals(2,$rule->rule_type_id);
		$this->assertEquals(2,$rule->site_id);
		$this->assertEquals(2,$rule->theatre_id);
		$this->assertEquals(2,$rule->subspecialty_id);
		$this->assertEquals(2,$rule->firm_id);

		$this->assertEquals(3,$rule->rule_order);
		$this->assertEquals(1,$rule->is_child);
		$this->assertEquals(0,$rule->show_warning);
		$this->assertEquals('flimflam',$rule->warning_text);
		$this->assertEquals(0,$rule->emphasis);
		$this->assertEquals(0,$rule->strong);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);
		$rule = \OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->resourceToModel($resource, $this->rules(0));
		$rule = \OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToResource()
	{
		$rule = $this->rules(0);
		$json = \Yii::app()->service->OphTrOperationbooking_Admission_Letter_Warning_Rule($rule->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $rule);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule, false);

		$this->verifyRule($rule, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, new \OphTrOperationbooking_Admission_Letter_Warning_Rule);
		$rule = \OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Admission_Letter_Warning_RuleService;
		$rule = $ps->jsonToModel($json, $this->rules(0));
		$rule = \OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($rule->id);

		$this->assertEquals(1,$rule->id);

		$this->verifyNewRule($rule);
	}
}
