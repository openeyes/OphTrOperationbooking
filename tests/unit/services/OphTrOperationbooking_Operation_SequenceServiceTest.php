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

class OphTrOperationbooking_Operation_SequenceServiceTest extends \CDbTestCase
{
	public $fixtures = array(
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'events' => 'Event',
		'event_types' => 'EventType',
		'disorders' => 'Disorder',
		'procedures' => 'Procedure',
		'element_type' => 'ElementType',
		'el_diagnosis' => 'Element_OphTrOperationbooking_Diagnosis',
		'el_operation' => 'Element_OphTrOperationbooking_Operation',
		'el_schedule' => 'Element_OphTrOperationbooking_ScheduleOperation',
		'procassign' => 'OphTrOperationbooking_Operation_Procedures',
		'bookings' => 'OphTrOperationbooking_Operation_Booking',
		'reasons' => 'OphTrOperationbooking_Operation_Cancellation_Reason',
		'theatres' => 'OphTrOperationbooking_Operation_Theatre',
		'statuses' => 'OphTrOperationbooking_Operation_Status',
		'sequences' => 'OphTrOperationbooking_Operation_Sequence',
		'sessions' => 'OphTrOperationbooking_Operation_Session',
		'wards' => 'OphTrOperationbooking_Operation_Ward',
		'specialties' => 'Specialty',
		'subspecialties' => 'Subspecialty',
		'rtts' => 'RTT',
		'rf_type' => 'ReferralType',
		'rf' => 'Referral',
	);

	public function testModelToResource()
	{
		$sequence = $this->sequences('sequence1');

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;

		$resource = $ps->modelToResource($sequence);

		$this->verifyResource($resource, $sequence);
	}

	public function verifyResource($resource, $sequence)
	{
		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_Sequence',$resource);
		$this->assertEquals($sequence->id,$resource->getId());

		$this->assertInstanceOf('services\FirmReference',$resource->firm_ref);
		$this->assertEquals($sequence->firm_id,$resource->firm_ref->getId());

		$this->assertInstanceOf('OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_TheatreReference',$resource->theatre_ref);
		$this->assertEquals($sequence->theatre_id,$resource->theatre_ref->getId());

		$this->assertInstanceOf('services\Date',$resource->start_date);
		$this->assertInstanceOf('services\DateTime',$resource->last_generate_date);
		$this->assertNull($sequence->end_date);
		$this->assertEquals($sequence->interval->name,$resource->interval);
		$this->assertEquals($sequence->weekdayText,$resource->weekday);
		$this->assertEquals($sequence->weekSelectionText,$resource->week_selection);

		foreach (array('start_time','end_time','consultant','paediatric','anaesthetist','general_anaesthetic','default_admission_time') as $field) {
			$this->assertEquals($sequence->$field,$resource->$field);
		}
	}

	public function getResource()
	{
		return \Yii::app()->service->OphTrOperationbooking_Operation_Sequence(1)->fetch();
	}

	public function testResourceToModel_NoSave_NoNewRecords()
	{
		$resource = $this->getResource();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Sequence, false);

		// no seqs please, we're British
		$this->assertEquals($total_seqs, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function testResourceToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Sequence, false);

		$this->verifySequence($sequence, $resource);
	}

	public function verifySequence($sequence, $resource, $keys=array())
	{
		$this->assertInstanceOf('OphTrOperationbooking_Operation_Sequence',$sequence);

		foreach ($keys as $key => $value) {
			if (is_null($value)) {
				$this->assertNull($sequence->$key);
			} else {
				$this->assertEquals($value, $sequence->$key);
			}
		}

		foreach ($sequence->getAttributes() as $key => $value) {
			if ($key != 'id' && !in_array($key,array_keys($keys)) && !in_array($key,array('created_date','last_modified_date'))) {
				$this->assertEquals($this->sequences('sequence1')->$key,$value);
			}
		}
	}

	public function getNewResource()
	{
		$resource = $this->getResource();
		$resource->start_time = '01:00:00';
		$resource->end_time = '05:00:00';
		$resource->weekday = 'Thursday';

		return $resource;
	}

	public function testResourceToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Sequence);

		// ooer!
		$this->assertEquals($total_seqs+1, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function testResourceToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Sequence);

		$this->verifySequence($sequence, $resource, array(
			'start_time' => '01:00:00',
			'end_time' => '05:00:00',
			'weekday' => 4,
		));
	}

	public function testResourceToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, new \OphTrOperationbooking_Operation_Sequence);
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk($sequence->id);

		$this->verifySequence($sequence, $resource, array(
			'start_time' => '01:00:00',
			'end_time' => '05:00:00',
			'weekday' => 4,
		));
	}

	public function getModifiedResource()
	{
		$resource = $this->getNewResource();

		$resource->firm_ref = \Yii::app()->service->Firm(2);
		$resource->theatre_ref = \Yii::app()->service->OphTrOperationbooking_Operation_Theatre(2);
		$resource->start_time = '03:00:00';
		$resource->end_time = '04:00:00';
		$resource->interval = '1 Week';
		$resource->week_selection = '1,3,4';
		$resource->consultant = 0;
		$resource->paediatric = 1;
		$resource->anaesthetist = 1;
		$resource->general_anaesthetic = 1;
		$resource->default_admission_time = '02:00:00';

		return $resource;
	}

	public function testResourceToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getModifiedResource();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, $this->sequences('sequence1'));

		$this->assertEquals($total_seqs, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function verifyModifiedSequence($sequence, $resource)
	{
		$this->verifySequence($sequence, $resource, array(
			'firm_id' => 2,
			'theatre_id' => 2,
			'start_time' => '03:00:00',
			'end_time' => '04:00:00',
			'interval_id' => \OphTrOperationbooking_Operation_Sequence_Interval::model()->find('name=?',array('1 Week'))->id,
			'week_selection' => 13,
			'consultant' => 0,
			'paediatric' => 1,
			'anaesthetist' => 1,
			'general_anaesthetic' => 1,
			'default_admission_time' => '02:00:00',
		));
	}

	public function testResourceToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, $this->sequences('sequence1'));

		$this->assertEquals(1,$sequence->id);

		$this->verifyModifiedSequence($sequence, $resource);
	}

	public function testResourceToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getModifiedResource();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->resourceToModel($resource, $this->sequences('sequence1'));
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk($sequence->id);

		$this->assertEquals(1,$sequence->id);

		$this->verifyModifiedSequence($sequence, $resource);
	}

	public function testJsonToResource()
	{
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk(1);
		$resource = \Yii::app()->service->OphTrOperationbooking_Operation_Sequence($sequence->id)->fetch();

		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$resource = $ps->jsonToResource($json);

		$this->verifyResource($resource, $sequence);
	}

	public function testJsonToModel_NoSave_NoNewRows()
	{
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk(1);
		$resource = \Yii::app()->service->OphTrOperationbooking_Operation_Sequence($sequence->id)->fetch();

		$json = $resource->serialise();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Sequence, false);

		$this->assertEquals($total_seqs, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function testJsonToModel_NoSave_ModelIsCorrect()
	{
		$resource = $this->getResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Sequence, false);

		$this->verifySequence($sequence, $resource);
	}

	public function testJsonToModel_Save_Create_ModelCountsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Sequence);

		// ooer!
		$this->assertEquals($total_seqs+1, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function testJsonToModel_Save_Create_ModelIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Sequence);

		$this->verifySequence($sequence, $resource, array(
			'start_time' => '01:00:00',
			'end_time' => '05:00:00',
			'weekday' => 4,
		));
	}

	public function testJsonToModel_Save_Create_DBIsCorrect()
	{
		$resource = $this->getNewResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, new \OphTrOperationbooking_Operation_Sequence);
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk($sequence->id);

		$this->verifySequence($sequence, $resource, array(
			'start_time' => '01:00:00',
			'end_time' => '05:00:00',
			'weekday' => 4,
		));
	}

	public function testJsonToModel_Save_Update_Modified_ModelCountsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$total_seqs = count(\OphTrOperationbooking_Operation_Sequence::model()->findAll());

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, $this->sequences('sequence1'));

		$this->assertEquals($total_seqs, count(\OphTrOperationbooking_Operation_Sequence::model()->findAll()));
	}

	public function testJsonToModel_Save_Update_ModelIsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, $this->sequences('sequence1'));

		$this->assertEquals(1,$sequence->id);

		$this->verifyModifiedSequence($sequence, $resource);
	}

	public function testJsonToModel_Save_Update_DBIsCorrect()
	{
		$resource = $this->getModifiedResource();
		$json = $resource->serialise();

		$ps = new \OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_SequenceService;
		$sequence = $ps->jsonToModel($json, $this->sequences('sequence1'));
		$sequence = \OphTrOperationbooking_Operation_Sequence::model()->findByPk($sequence->id);

		$this->assertEquals(1,$sequence->id);

		$this->verifyModifiedSequence($sequence, $resource);
	}
}
