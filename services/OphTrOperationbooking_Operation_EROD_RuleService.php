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

class OphTrOperationbooking_Operation_EROD_RuleService extends \services\DeclarativeModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'OphTrOperationbooking_Operation_EROD_Rule';

	static public $model_map = array(
		'OphTrOperationbooking_Operation_EROD_Rule' => array(
			'fields' => array(
				'subspecialty_ref' => array(self::TYPE_REF, 'subspecialty_id', 'Subspecialty'),
				'items' => array(self::TYPE_LIST, 'items', '\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_EROD_Rule_Item', 'OphTrOperationbooking_Operation_EROD_Rule_Item', array('erod_rule_id' => 'primaryKey')),
			),
		),
		'\OEModule\OphTrOperationbooking\services\OphTrOperationbooking_Operation_EROD_Rule_Item' => array(
			'ar_class' => 'OphTrOperationbooking_Operation_EROD_Rule_Item',
			'related_objects' => array(
				'rule' => array('erod_rule_id', 'OphTrOperationbooking_Operation_EROD_Rule', 'save' => 'no'),
			),
			'fields' => array(
				'item_type' => 'item_type',
				'item_id' => 'item_id',
			),
		),
	);

	public function search(array $params)
	{
	}

	public function resourceToModel_AfterSave($model)
	{
		$mc = new \services\ModelConverter($this);
		$parser = new \services\DeclarativeTypeParser_List($mc);
		$parser->resourceToModel_RelatedObjects($model, 'items', array('erod_rule_id' => 'primaryKey'), true);
	}
}
