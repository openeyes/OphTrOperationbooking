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
 * This is the model class for table "ophtroperationbooking_operation_sequence".
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property integer $theatre_id
 * @property date $start_date
 * @property time $start_time
 * @property time $end_time
 * @property date $end_date
 *
 * The followings are the available model relations:
 *
 * @property Site $site
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 *
 */

class OphTrOperationbooking_Operation_Sequence extends BaseActiveRecord
{
	const SELECT_1STWEEK = 1;
	const SELECT_2NDWEEK = 2;
	const SELECT_3RDWEEK = 4;
	const SELECT_4THWEEK = 8;
	const SELECT_5THWEEK = 16;

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
		return 'ophtroperationbooking_operation_sequence';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('theatre_id, start_date, start_time, end_time, interval_id, weekday', 'required'),
			array('theatre_id', 'length', 'max'=>10),
			array('end_date, week_selection, consultant, paediatric, anaesthetist, general_anaesthetic, firm_id', 'safe'),
			array('start_date', 'date', 'format'=>'yyyy-MM-dd'),
			array('start_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_time', 'date', 'format'=>array('H:mm', 'H:mm:ss')),
			array('end_date', 'checkDates'),
			array('end_time', 'checkTimes'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, theatre_id, start_date, start_time, end_time, end_date, consultant, paediatric, anaesthetist, interval_id, weekday, week_selection, firm_id, site_id', 'safe', 'on'=>'search'),
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
			'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
			'firmAssignment' => array(self::HAS_ONE, 'SequenceFirmAssignment', 'sequence_id'),
			'firm' => array(self::HAS_ONE, 'Firm', 'firm_id', 'through' => 'firmAssignment'),
			'sessions' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Session', 'sequence_id'),
			'interval' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Sequence_Interval', 'interval_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'theatre_id' => 'Theatre',
				'interval_id' => 'Interval',
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

	public function checkDates() {
		if (!empty($this->end_date)) {
			$start = strtotime($this->start_date);
			$end = strtotime($this->end_date);

			if ($end < $start) {
				$this->addError('end_date', 'End date must be after the start date.');
			}
		}
	}

	public function checkTimes() {
		$start = strtotime($this->start_time);
		$end = strtotime($this->end_time);

		if ($end <= $start) {
			$this->addError('end_time', 'End time must be after the start time.');
		}
	}

	public function getWeekOccurrences($weekday, $weekSelection, $startTimestamp, $endTimestamp, $startDate, $endDate) {
		$dates = array();
		$month = strtotime(date('Y-m-01',$startTimestamp));
		$weekday_options = $this->getWeekdayOptions();
		$weekday_string = $weekday_options[$weekday];
		while($month <= $endTimestamp) {
			$day = strtotime("first $weekday_string of", $month);
			for ($i = self::SELECT_1STWEEK; $i <= self::SELECT_5THWEEK; $i *= 2) {
				// Only add date if it is between start and end dates, and is a selected week. Also check we haven't rolled over into the next month (4 week months) 
				if($day >= $startTimestamp && $day <= $endTimestamp && $day <= strtotime('last day of', $month) && ($weekSelection & $i)) {
					$dates[] = date('Y-m-d',$day);
				}
				$day = strtotime("+1 week", $day);
			}
			$month = strtotime("+1 month", $month);
		}
		return $dates;
	}

	public function getWeekdayOptions() {
		return array(
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
			7 => 'Sunday',
		);
	}
	
	public function getIntervalOptions() {
		$intervals = OphTrOperationbooking_Operation_Sequence_Interval::model()->findAll();
		return CHtml::listData($intervals, 'id', 'name');
	}

	public function getTheatreOptions() {
		$theatres = OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order' => 'site_id, display_order'));
		return CHtml::listData($theatres, 'id', 'NameWithSite');
	}
	
	public function getFirmOptions() {
		return Firm::model()->getListWithSpecialties();
	}
	
}
