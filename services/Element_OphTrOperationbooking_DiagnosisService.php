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

class Element_OphTrOperationbooking_DiagnosisService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'Element_OphTrOperationbooking_Diagnosis';

	static protected $model_map = array(
		'Element_OphTrOperationbooking_Diagnosis' => array(
			'reference_objects' => array(
				'eye' => array('eye_id', 'Eye', array('name')),
				'disorder' => array('disorder_id', 'Disorder', array('term')),
			),
			'fields' => array(
				'event_ref' => array(self::TYPE_REF, 'event_id', 'Event'),
				'eye' => 'eye.name',
				'disorder' => 'disorder.term',
			),
		),
	);

	public function search(array $params)
	{
	}
}
