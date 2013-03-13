<?php /**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationbooking_letter_contact_rule".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property integer $contact_type_id
 * @property integer $site_id
 * @property integer $subspecialty_id
 * @property integer $theatre_id
 * @property integer $firm_id
 * @property string $telephone
 *
 * The followings are the available model relations:
 *
 * @property Site $site
 * @property Subspecialty $subspecialty
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 * @property OphTrOperationbooking_Operation_Firm $firm
 */

class OphTrOperationbooking_Letter_Contact_Rule extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ophtroperationbooking_letter_contact_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, contact_type_id, site_id, subspecialty_id, theatre_id, refuse_telephone, health_telephone', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, contact_type_id, site_id, subspecialty_id, theatre_id', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
			'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'children' => array(self::HAS_MANY, 'OphTrOperationbooking_Letter_Contact_Rule', 'parent_rule_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	public function applies($site_id, $subspecialty_id, $theatre_id, $firm_id) {
		foreach (array('site_id','subspecialty_id','theatre_id','firm_id') as $field) {
			if ($this->{$field} && $this->{$field} != ${$field}) {
				return false;
			}
		}

		return true;
	}

	public function parse($site_id, $subspecialty_id, $theatre_id, $firm_id) {
		foreach ($this->children as $child_rule) {
			if ($child_rule->applies($site_id, $subspecialty_id, $theatre_id, $firm_id)) {
				return $child_rule->parse($site_id, $subspecialty_id, $theatre_id, $firm_id);
			}
		}

		return $this;
	}
}
