<?php /**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "ophtroperation_operation_booking".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property integer $element_id
 * @property integer $session_id
 * @property integer $display_order
 * @property integer $ward_id
 * @property time $admission_time
 * @property integer confirmed
 *
 * The followings are the available model relations:
 *
 * @property Session $session
 * @property Element_OphTrOperation_Operation $operation
 * @property User $user
 * @property User $usermodified
 * @property Ward $ward
 *
 */

class OphTrOperation_Operation_Booking extends BaseActiveRecord
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
		return 'ophtroperation_operation_booking';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('element_id, session_id, display_order, ward_id, admission_time, confirmed, session_date, session_start_time, session_end_time, session_theatre_id, cancellation_date, cancellation_reason_id, cancellation_comment, cancellation_user_id', 'safe'),
			array('element_id', 'required'),
			array('display_order', 'numerical', 'integerOnly'=>true),
			array('ward_id', 'numerical', 'integerOnly'=>true),
			array('element_id, session_id', 'length', 'max'=>10),
			array('admission_time', 'match', 'pattern' => '/^[0-9]{1,2}.*?[0-9]{2}$/'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, element_id, session_id, display_order, ward_id, admission_time, confirmed', 'safe', 'on' => 'search'),
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
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'ward' => array(self::BELONGS_TO, 'Ward', 'ward_id'),
			'session' => array(self::BELONGS_TO, 'Session', 'session_id'),
			'operation' => array(self::BELONGS_TO, 'Element_OphTrOperation_Operation', 'element_id'),
			'theatre' => array(self::BELONGS_TO, 'Theatre', 'session_theatre_id'),
			'cancellationReason' => array(self::BELONGS_TO, 'OphTrOperation_Operation_Cancellation_Reason', 'cancellation_reason_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'element_id' => 'Element Operation',
			'session_id' => 'Session',
			'display_order' => 'Display Order',
			'ward_id' => 'Ward',
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
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	public function getCancellationReasonWithComment() {
		$return = $this->cancellationReason->text;
		if ($this->cancellation_comment) {
			$return .= " ($this->cancellation_comment)";
		}
		return $return;
	}

	public function audit($target, $action, $data=null, $log=false, $properties=array()) {
		$properties['event_id'] = $this->operation->event_id;
		$properties['episode_id'] = $this->operation->event->episode_id;
		$properties['patient_id'] = $this->operation->event->episode->patient_id;

		return parent::audit($target, $action, $data, $log, $properties);
	}
}
?>
