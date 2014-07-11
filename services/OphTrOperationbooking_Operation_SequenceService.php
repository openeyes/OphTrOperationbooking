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

namespace OEModule\OphTrOperationbooking\services;

class OphTrOperationbooking_Operation_SequenceService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'OphTrOperationbooking_Operation_Sequence';

	static public $model_map = array(
		'OphTrOperationbooking_Operation_Sequence' => array(
			'reference_objects' => array(
				'interval' => array('interval_id', 'OphTrOperationbooking_Operation_Sequence_Interval', array('name')),
			),
			'fields' => array(
				'firm_ref' => array(self::TYPE_REF, 'firm_id', 'Firm'),
				'theatre_ref' => array(self::TYPE_REF, 'theatre_id', 'OphTrOperationbooking_Operation_Theatre'),
				'start_date' => array(self::TYPE_SIMPLEOBJECT, 'start_date', 'Date'),
				'start_time' => 'start_time',
				'end_date' => array(self::TYPE_SIMPLEOBJECT, 'end_date', 'Date'),
				'end_time' => 'end_time',
				'interval' => 'interval.name',
				'weekday' => array(self::TYPE_SERVICEMETHOD, 'weekday'),
				'week_selection' => array(self::TYPE_SERVICEMETHOD, 'week_selection'),
				'consultant' => 'consultant',
				'paediatric' => 'paediatric',
				'anaesthetist' => 'anaesthetist',
				'general_anaesthetic' => 'general_anaesthetic',
				'last_generate_date' => array(self::TYPE_SIMPLEOBJECT, 'last_generate_date', 'DateTime'),
				'default_admission_time' => 'default_admission_time',
			),
		),
	);

	public function search(array $params)
	{
	}

	public function modelToResource_weekday($weekday_int)
	{
		$sequence = new \OphTrOperationbooking_Operation_Sequence;
		$sequence->weekday = $weekday_int;

		return $sequence->weekdayText;
	}

	public function modelToResource_week_selection($week_selection)
	{
		$sequence = new \OphTrOperationbooking_Operation_Sequence;
		$sequence->week_selection = $week_selection;

		return $sequence->weekSelectionText;
	}

	public function resourceToModel_weekday($weekday_text)
	{
		$sequence = new \OphTrOperationbooking_Operation_Sequence;

		foreach ($sequence->weekDayOptions as $i => $option) {
			if ($weekday_text == $option) {
				return $i;
			}
		}

		return null;
	}

	public function resourceToModel_week_selection($week_selection_text)
	{
		$week_selection = 0;
		$selected_weeks = explode(',',$week_selection_text);

		foreach (array(1,2,4,8,16) as $i => $n) {
			if (in_array($i+1,$selected_weeks)) {
				$week_selection += $n;
			}
		}

		return $week_selection;
	}
}
