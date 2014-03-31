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
 * This is the model class for table "ophtroperationbooking_operation_session".
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
 * @property OphTrOperationbooking_Operation_Sequence $sequence
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 *
 */

class OphTrOperationbooking_Operation_Session extends BaseActiveRecordVersioned
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className
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
		return 'ophtroperationbooking_operation_session';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sequence_id, date, start_time, end_time, theatre_id', 'required'),
			array('sequence_id, theatre_id', 'length', 'max' => 10),
			array('comments, available, consultant, paediatric, anaesthetist, general_anaesthetic, firm_id, theatre_id, start_time, end_time, default_admission_time', 'safe'),
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
			'session_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'session_usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'sequence' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Sequence', 'sequence_id'),
			'activeBookings' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Booking', 'session_id',
				'on' => 'activeBookings.booking_cancellation_date is null',
				'order' => 'activeBookings.display_order ASC',
				'with' => array(
					'operation',
					'operation.event' => array('joinType' => 'join'),
					'operation.event.episode' => array('joinType' => 'join')
				),
			),
		);
	}

	public function getActiveBookingsForWard($ward_id = null)
	{
		$criteria = array(
			'with' => array(
				'operation',
				'operation.anaesthetic_type',
				'operation.priority',
				'operation.event' => array('joinType' => 'join'),
				'operation.event.episode' => array('joinType' => 'join'),
				'operation.event.episode.patient',
				'operation.event.episode.patient.episodes',
				'operation.event.episode.patient.contact',
				'operation.event.episode.patient.allergies',
				'operation.procedures',
				'operation.op_usermodified',
				'operation.op_user',
				'operation.eye',
				'ward',
				'user',
			)
		);
		if((int)$ward_id) {
			$criteria['condition'] = 'ward.id = :ward_id';
			$criteria['params'][':ward_id'] = (int)$ward_id;
		}
		return $this->activeBookings($criteria);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'sequence_id' => 'Sequence ID',
			'firm_id' => 'Firm',
			'theatre_id' => 'Theatre',
			'start_time' => 'Start time',
			'end_time' => 'End time',
			'general_anaesthetic' => 'General anaesthetic',
			'default_admission_time' => 'Default admission time',
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

	public function getDuration()
	{
		return (mktime(substr($this->end_time,0,2),substr($this->end_time,3,2),0,1,1,date('Y')) - mktime(substr($this->start_time,0,2),substr($this->start_time,3,2),0,1,1,date('Y'))) / 60;
	}

	public function getBookedMinutes()
	{
		$total = 0;

		foreach ($this->activeBookings as $booking) {
			$total += $booking->operation->total_duration;
		}

		return $total;
	}

	public function getAvailableMinutes()
	{
		return $this->duration - $this->bookedminutes;
	}

	public function getMinuteStatus()
	{
		return $this->availableMinutes >= 0 ? 'available' : 'overbooked';
	}

	public function getStatus()
	{
		return $this->availableMinutes >= 0 ? 'available' : 'full';
	}

	public function getTimeSlot()
	{
		return date('H:i',strtotime($this->start_time)) . ' - ' . date('H:i',strtotime($this->end_time));
	}

	public function getFirmName()
	{
		if ($this->firm) {
			return $this->firm->name . ' (' . $this->firm->serviceSubspecialtyAssignment->subspecialty->name . ')';
		} else {
			return 'Emergency List';
		}
	}

	public function getTheatreName()
	{
		if ($this->theatre) {
			return $this->theatre->name . ' (' . $this->theatre->site->short_name . ')';
		} else {
			return 'None';
		}
	}

	public function operationBookable($operation)
	{
		if (!$this->available) {
			return false;
		}

		$helper = new OphTrOperationbooking_BookingHelper;
		if ($helper->checkSessionCompatibleWithOperation($this, $operation)) {
			return false;
		}

		if ($this->date < date('Y-m-d')) {
			return false;
		}

		return true;
	}

	public function unbookableReason($operation)
	{
		if (!$this->available) {
			return "This session is unavailable at this time";
		}

		$helper = new OphTrOperationbooking_BookingHelper;
		if (($errors = $helper->checkSessionCompatibleWithOperation($this, $operation))) {
			switch ($errors[0]) {
				case $helper::ANAESTHETIST_REQUIRED:
					return "The operation requires an anaesthetist, this session doesn't have one and so cannot be booked into.";
				case $helper::CONSULTANT_REQUIRED:
					return "The operation requires a consultant, this session doesn't have one and so cannot be booked into.";
				case $helper::PAEDIATRIC_SESSION_REQUIRED:
					return "The operation is for a paediatric patient, this session isn't paediatric and so cannot be booked into.";
				case $helper::GENERAL_ANAESTHETIC_REQUIRED:
					return "The operation requires general anaesthetic, this session doesn't have this and so cannot be booked into.";
			}
		}

		if ($this->date < date('Y-m-d')) {
			return "This session is in the past and so cannot be booked into.";
		}
	}

	public function getWeekdayText()
	{
		return date('l',strtotime($this->date));
	}

	protected function beforeValidate()
	{
		if ($this->date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$this->date)) {
			$this->date = date('Y-m-d',strtotime($this->date));
		}

		// Ensure we are still compatible with any active bookings
		$helper = new OphTrOperationbooking_BookingHelper;
		foreach ($this->activeBookings as $booking) {
			foreach ($helper->checkSessionCompatibleWithOperation($this, $booking->operation) as $error) {
				switch ($error) {
					case $helper::ANAESTHETIST_REQUIRED:
						$this->addError('anaesthetist','One or more active bookings require an anaesthetist');
						break;
					case $helper::CONSULTANT_REQUIRED:
						$this->addError('consultant','One or more active bookings require a consultant');
						break;
					case $helper::PAEDIATRIC_SESSION_REQUIRED:
						$this->addError('paediatric','One or more active bookings are for a child');
						break;
					case $helper::GENERAL_ANAESTHETIC_REQUIRED:
						$this->addError('general_anaesthetic','One or more active bookings require general anaesthetic');
				}
			}
		}

		return parent::beforeValidate();
	}

	protected function beforeSave()
	{
		if ($this->date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$this->date)) {
			$this->date = date('Y-m-d',strtotime($this->date));
		}

		return parent::beforeSave();
	}

	/**
	 * Dissociate the session from cancelled bookings and ERODs before deletion
	 */
	protected function beforeDelete()
	{
		OphTrOperationbooking_Operation_Booking::model()->updateAll(
			array('session_id' => null),
			'session_id = :session_id and booking_cancellation_date is not null',
			array(':session_id' => $this->id)
		);

		Ophtroperationbooking_Operation_EROD::model()->updateAll(
			array('session_id' => null),
			'session_id = :session_id',
			array(':session_id' => $this->id)
		);

		return parent::beforeDelete();
	}

	/**
	 * Get the next session for the given firm id
	 *
	 * @param $firm_id
	 * @return OphTrOperationbooking_Operation_Session|null
	 */
	public static function getNextSessionForFirmId($firm_id)
	{
		$criteria = new CDbCriteria;
		$criteria->addCondition("firm_id = :firm_id and date >= :date");
		$criteria->params = array(
			'firm_id' => $firm_id,
			'date' => date('Y-m-d'),
		);
		$criteria->order = 'date asc';

		if ($session = OphTrOperationbooking_Operation_Session::model()->find($criteria)) {
			return $session;
		}
	}

}
