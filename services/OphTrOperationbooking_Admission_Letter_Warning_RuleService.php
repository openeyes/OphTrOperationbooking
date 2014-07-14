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

class OphTrOperationbooking_Admission_Letter_Warning_RuleService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'OphTrOperationbooking_Admission_Letter_Warning_Rule';

	static public $model_map = array(
		'OphTrOperationbooking_Admission_Letter_Warning_Rule' => array(
			'fields' => array(
				'rule_type_ref' => array(self::TYPE_REF, 'rule_type_id', 'OphTrOperationbooking_Admission_Letter_Warning_Rule_Type'),
				'parent_rule_ref' => array(self::TYPE_REF, 'parent_rule_id', 'OphTrOperationbooking_Admission_Letter_Warning_Rule'),
				'rule_order' => 'rule_order',
				'site_ref' => array(self::TYPE_REF, 'site_id', 'Site'),
				'theatre_ref' => array(self::TYPE_REF, 'theatre_id', 'OphTrOperationbooking_Operation_Theatre'),
				'subspecialty_ref' => array(self::TYPE_REF, 'subspecialty_id', 'Subspecialty'),
				'is_child' => 'is_child',
				'show_warning' => 'show_warning',
				'warning_text' => 'warning_text',
				'emphasis' => 'emphasis',
				'strong' => 'strong',
				'firm_ref' => array(self::TYPE_REF, 'firm_id', 'Firm'),
			),
		),
	);

	public function search(array $params)
	{
	}
}
