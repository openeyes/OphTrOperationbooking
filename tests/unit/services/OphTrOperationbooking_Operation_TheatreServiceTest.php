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

class OphTrOperationbooking_Operation_TheatreServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'sites' => 'Site',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
		'theatres' => 'OphTrOperationbooking_Operation_Theatre',
	);

	public function testModelToResource()
	{
		$theatre = $this->theatres('th1');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;

		$resource = $ps->modelToResource($theatre);

		$this->verifyResource($resource, $theatre);
	}

	public function verifyResource($resource, $theatre)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Theatre',$resource);
		$this->assertEquals($theatre->id,$resource->getId());

		$this->assertInstanceOf('services\SiteReference',$resource->site_ref);
		$this->assertEquals($theatre->site_id,$resource->site_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_WardReference',$resource->ward_ref);
		$this->assertEquals($theatre->ward_id,$resource->ward_ref->getId());

		$this->assertEquals('T1',$resource->code);
		$this->assertEquals(1,$resource->active);
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$t = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Theatre, false);

		$this->assertEquals($t, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Theatre, false);

		$this->verifyTheatre($theatre, $resource);
	}

	public function verifyTheatre($theatre, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$theatre);
		$this->assertEquals($resource->site_ref->getId(),$theatre->site_id);
		$this->assertEquals($resource->ward_ref->getId(),$theatre->ward_id);
		$this->assertEquals($resource->code,$theatre->code);
		$this->assertEquals($resource->active,$theatre->active);
	}

	public function getNewResource()
	{
		$resource = $this->getResource();

		$resource->site_ref = \Yii::app()->service->Site(2);
		$resource->ward_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Ward(6);
		$resource->code = 'AA1';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$t = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Theatre);

		$this->assertEquals($t+1, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
	}

	public function verifyNewTheatre($theatre)
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Theatre',$theatre);

		$this->assertEquals(2,$theatre->site_id);
		$this->assertEquals(6,$theatre->ward_id);
		$this->assertEquals(1,$theatre->active);
		$this->assertEquals('AA1',$theatre->code);
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Theatre);

		$this->verifyNewTheatre($theatre);
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Theatre);
		$theatre = \OphTrOperationbooking_Operation_Theatre::model()->findByPk($theatre->id);

		$this->verifyNewTheatre($theatre);
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$ts = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());
		$tb = count(\OphTrOperationbooking_Operation_Booking::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, $this->theatres('th1'));

		$this->assertEquals($ts, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
		$this->assertEquals($tb, count(\OphTrOperationbooking_Operation_Booking::model()->findAll()));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, $this->theatres('th1'));

		$this->assertEquals(1,$theatre->id);

		$this->verifyNewTheatre($theatre);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->resourceToModel($resource, $this->theatres('th1'));
		$theatre = \OphTrOperationbooking_Operation_Theatre::model()->findByPk($theatre->id);

		$this->assertEquals(1,$theatre->id);

		$this->verifyNewTheatre($theatre);
	}

	public function testJsonToResource()
	{
		$theatre = $this->theatres('th1');
		$json = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre($theatre->id)->fetch()->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;

		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $theatre);
	}

	public function testJsonToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Theatre, false);

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Theatre, false);

		$this->verifyTheatre($theatre, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Theatre);

		$this->assertEquals($total_s+1, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Theatre);

		$this->verifyNewTheatre($theatre);
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Theatre);
		$theatre = \OphTrOperationbooking_Operation_Theatre::model()->findByPk($theatre->id);

		$this->verifyNewTheatre($theatre);
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_s = count(\OphTrOperationbooking_Operation_Theatre::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, $this->theatres('th1'));

		$this->assertEquals($total_s, count(\OphTrOperationbooking_Operation_Theatre::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, $this->theatres('th1'));

		$this->assertEquals(1,$theatre->id);

		$this->verifyNewTheatre($theatre);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreService;
		$theatre = $ps->jsonToModel($json, $this->theatres('th1'));
		$theatre = \OphTrOperationbooking_Operation_Theatre::model()->findByPk($theatre->id);

		$this->assertEquals(1,$theatre->id);

		$this->verifyNewTheatre($theatre);
	}
}
