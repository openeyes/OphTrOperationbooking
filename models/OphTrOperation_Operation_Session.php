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
 * This is the model class for table "ophtroperation_operation_session".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property integer $sequence_id
 * @property date $date
 * @property time $start_time
 * @property time $end_time
 * @property string $comments
 * @property integer $available
 * @property boolean $consultant
 * @property boolean $paediatric
 * @property boolean $anaesthetist
 * @property boolean $general_anaesthetic
 * @property integer $theatre_id
 *
 * The followings are the available model relations:
 *
 * @property Sequence $sequence
 * @property Theatre $theatre
 *
 */

class OphTrOperation_Operation_Session extends BaseActiveRecord
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
		return 'ophtroperation_operation_session';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sequence_id, date, start_time, end_time', 'required'),
			array('sequence_id, theatre_id', 'length', 'max' => 10),
			array('comments, available, consultant, paediatric, anaesthetist, general_anaesthetic, firm_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sequence_id, theatre_id, date, start_time, end_time, comments, available, firm_id, site_id, weekday, consultant, paediatric, anaesthetist, general_anaesthetic', 'safe', 'on'=>'search'),
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
			'theatre' => array(self::BELONGS_TO, 'Theatre', 'theatre_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
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

	public static function findByDateAndFirmID($monthStart, $minDate, $firmId) {
		if ($firmId !== null) {
			$firm = Firm::model()->findByPk($firmId);
			if (empty($firm)) {
				throw new Exception('Firm id is invalid.');
			}
		}
		if (substr($minDate,0,8) == substr($monthStart,0,8)) {
			$startDate = $minDate;
		} else {
			$startDate = $monthStart;
		}
		$monthEnd = substr($monthStart,0,8) . date('t', strtotime($monthStart));

		if ($firmId === null) {
			$firmSql = 's.firm_id IS NULL';
		} else {
			$firmSql = "s.firm_id = $firmId";
		}

		$sessions = Yii::app()->db->createCommand()
			->select("s.*, TIMEDIFF(s.end_time, s.start_time) AS session_duration, COUNT(a.id) AS bookings, SUM(o.total_duration) AS bookings_duration")
			->from("ophtroperation_operation_session s")
			->join("ophtroperation_operation_theatre t","s.theatre_id = t.id")
			->leftJoin("ophtroperation_operation_booking a","s.id = a.session_id")
			->leftJoin("et_ophtroperation_operation o","a.element_id = o.id")
			->leftJoin("event e","o.event_id = e.id")
			->where("s.available = 1 AND s.date BETWEEN CAST('$startDate' AS DATE) AND CAST('$monthEnd' AS DATE) AND $firmSql")
			->group("s.id")
			->order("WEEKDAY(DATE) ASC")
			->queryAll();

		return $sessions;
	}

	public function getDuration() {
		return (mktime(substr($this->end_time,0,2),substr($this->end_time,3,2),0,1,1,date('Y')) - mktime(substr($this->start_time,0,2),substr($this->start_time,3,2),0,1,1,date('Y'))) / 60;
	}

	public function getBookedMinutes() {
		$total = 0;

		foreach (Yii::app()->db->createCommand()
			->select("o.total_duration")
			->from("et_ophtroperation_operation o")
			->join("ophtroperation_operation_booking","ophtroperation_operation_booking.element_id = o.id")
			->where("ophtroperation_operation_booking.session_id = :sessionId",array(':sessionId' => $this->id))
			->queryAll() as $operation) {
			$total += $operation['total_duration'];
		}

		return $total;
	}

	public function getAvailableMinutes() {
		return $this->duration - $this->bookedminutes;
	}

	public function getMinuteStatus() {
		return $this->available >= 0 ? 'available' : 'overbooked';
	}
}
?>
