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
 * This is the model class for table "ophtroperation_operation_theatre".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property string $name
 * @property integer $site_id
 * @property string $code
 *
 * The followings are the available model relations:
 *
 * @property Site $site
 *
 */

class OphTrOperation_Operation_Theatre extends BaseActiveRecord
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
		return 'ophtroperation_operation_theatre';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, site_id, code', 'safe'),
			array('name, site_id, code', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, site_id, code', 'safe', 'on' => 'search'),
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
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
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
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	public static function findByDateAndFirmID($date, $firmId) {
		if ($firmId === null) {
			$firmSql = 's.firm_id IS NULL';
		} else {
			if (!$firm = Firm::model()->findByPk($firmId)) {
				throw new Exception('Firm id is invalid.');
			}
			$firmSql = "s.firm_id = $firmId";
		}

		$sessions = Yii::app()->db->createCommand()
			->select("t.*, s.start_time, s.end_time, s.id AS session_id, s.consultant, s.anaesthetist, s.paediatric, s.general_anaesthetic, TIMEDIFF(s.end_time, s.start_time) AS session_duration, COUNT(a.id) AS bookings, SUM(o.total_duration) AS bookings_duration")
			->from("ophtroperation_operation_session s")
			->join("ophtroperation_operation_theatre t","s.theatre_id = t.id")
			->leftJoin("ophtroperation_operation_booking a","s.id = a.session_id and a.cancellation_date is null")
			->leftJoin("et_ophtroperation_operation o","a.element_id = o.id")
			->leftJoin("event e","o.event_id = e.id")
			->where("s.available = 1 and s.date = :date and $firmSql and (e.deleted = 0 or e.deleted is null)",array(':date' => $date))
			->group("s.id")
			->order("s.start_time")
			->queryAll();

		return $sessions;
	}
}
?>
