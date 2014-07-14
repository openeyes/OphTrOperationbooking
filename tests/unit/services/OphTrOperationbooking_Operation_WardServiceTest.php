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

class OphTrOperationbooking_Operation_WardServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'sites' => 'Site',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
	);

	public function testModelToResource()
	{
		$ward = $this->wards('ward1');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;

		$resource = $ps->modelToResource($ward);

		$this->verifyResource($resource, $ward);
	}

	public function verifyResource($resource, $ward)
	{
		$this->assertInstanceOf('services\SiteReference',$resource->site_ref);
		$this->assertEquals($ward->site_id,$resource->site_ref->getId());

		foreach (array('name','long_name','directions','code','restriction','display_order','active') as $field) {
			$this->assertEquals($this->wards('ward1')->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Ward(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Ward, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Ward, false);

		$this->verifyWard($ward, $resource);
	}

	public function verifyWard($ward, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$ward);
		$this->assertEquals($resource->site_ref->getId(),$ward->site_id);

		foreach (array('name','long_name','directions','code','restriction','display_order','active') as $field) {
			$this->assertEquals($resource->$field,$ward->$field);
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->site_ref = \Yii::app()->service->Site(2);
		$resource->name = '4141';
		$resource->long_name = '41414141';
		$resource->directions = 'round the corner';
		$resource->code = '311';
		$resource->restriction = \OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + \OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + \OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT;
		$resource->display_order = 7;

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Ward);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
	}

	public function verifyNewWard($ward)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Ward',$ward);

		$this->assertEquals(2,$ward->site_id);
		$this->assertEquals('4141',$ward->name);
		$this->assertEquals('41414141',$ward->long_name);
		$this->assertEquals('round the corner',$ward->directions);
		$this->assertEquals('311',$ward->code);
		$this->assertEquals(\OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + \OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + \OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT,$ward->restriction);
		$this->assertEquals(7,$ward->display_order);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Ward);

		$this->verifyNewWard($ward);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Ward);
		$ward = \OphTrOperationbooking_Operation_Ward::model()->findByPk($ward->id);

		$this->verifyNewWard($ward);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, $this->wards('ward1'));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, $this->wards('ward1'));

		$this->assertEquals(1,$ward->id);

		$this->verifyNewWard($ward);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->resourceToModel($resource, $this->wards('ward1'));
		$ward = \OphTrOperationbooking_Operation_Ward::model()->findByPk($ward->id);

		$this->assertEquals(1,$ward->id);

		$this->verifyNewWard($ward);
	}

	public function testJsonToResource()
	{
		$ward = $this->wards('ward1');
		$json = \Yii::app()->service->OphTrOperationbooking_Operation_Ward($ward->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $ward);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Ward, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Ward, false);

		$this->verifyWard($ward, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Ward);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Ward);

		$this->verifyNewWard($ward);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Ward);
		$ward = \OphTrOperationbooking_Operation_Ward::model()->findByPk($ward->id);

		$this->verifyNewWard($ward);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Ward::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, $this->wards('ward1'));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Ward::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, $this->wards('ward1'));

		$this->assertEquals(1,$ward->id);

		$this->verifyNewWard($ward);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardService;
		$ward = $ps->jsonToModel($json, $this->wards('ward1'));
		$ward = \OphTrOperationbooking_Operation_Ward::model()->findByPk($ward->id);

		$this->assertEquals(1,$ward->id);

		$this->verifyNewWard($ward);
	}
}
