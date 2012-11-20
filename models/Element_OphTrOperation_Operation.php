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
	 * This is the model class for table "et_ophtroperation_operation".
	 *
	 * The followings are the available columns in table:
	 * @property string $id
	 * @property integer $event_id
	 * @property integer $eye_id
	 * @property integer $consultant_required
	 * @property integer $anaesthetic_type_id
	 * @property integer $overnight_stay
	 * @property integer $site_id
	 * @property integer $priority_id
	 * @property string $decision_date
	 * @property string $comments
	 *
	 * The followings are the available model relations:
	 *
	 * @property ElementType $element_type
	 * @property EventType $eventType
	 * @property Event $event
	 * @property User $user
	 * @property User $usermodified
	 * @property Eye $eye
	 * @property OphTrOperation_Operation_Procedures $procedures
	 * @property AnaestheticType $anaesthetic_type
	 * @property Site $site
	 * @property Element_OphTrOperation_Operation_Priority $priority
	 */

	class Element_OphTrOperation_Operation extends BaseEventTypeElement
	{
		const LETTER_INVITE = 0;
		const LETTER_REMINDER_1 = 1;
		const LETTER_REMINDER_2 = 2;
		const LETTER_GP = 3;
		const LETTER_REMOVAL = 4;

		// these reflect an actual status, relating to actions required rather than letters sent
		const STATUS_WHITE = 0; // no action required.	the default status.
		const STATUS_PURPLE = 1; // no invitation letter has been sent
		const STATUS_GREEN1 = 2; // it's two weeks since an invitation letter was sent with no further letters going out
		const STATUS_GREEN2 = 3; // it's two weeks since 1st reminder was sent with no further letters going out
		const STATUS_ORANGE = 4; // it's two weeks since 2nd reminder was sent with no further letters going out
		const STATUS_RED = 5; // it's one week since gp letter was sent and they're still on the list
		const STATUS_NOTWAITING = null;

		public $service;

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
			return 'et_ophtroperation_operation';
		}

		/**
		 * @return array validation rules for model attributes.
		 */
		public function rules()
		{
			// NOTE: you should only define rules for those attributes that
			// will receive user inputs.
			return array(
				array('event_id, eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date, comments, anaesthetist_required, total_duration, status_id, cancellation_date, cancellation_reason_id, cancellation_comment, cancellation_user_id', 'safe'),
				array('eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date, comments, ', 'safe', 'on' => 'search'),
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
			'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
			'procedures' => array(self::HAS_MANY, 'OphTrOperation_Operation_Procedures', 'element_id'),
			'anaesthetic_type' => array(self::BELONGS_TO, 'AnaestheticType', 'anaesthetic_type_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'priority' => array(self::BELONGS_TO, 'OphTrOperation_Operation_Priority', 'priority_id'),
			'status' => array(self::BELONGS_TO, 'OphTrOperation_Operation_Status', 'status_id'),
			'erod' => array(self::HAS_ONE, 'OphTrOperation_Operation_EROD', 'element_id'),
			'date_letter_sent' => array(self::HAS_ONE, 'OphTrOperation_Operation_Date_Letter_Sent', 'element_id', 'order' => 'date_letter_sent.id DESC'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'eye_id' => 'Eyes',
			'procedures' => 'Operations',
			'consultant_required' => 'Consultant required',
			'anaesthetic_type_id' => 'Anaesthetic type',
			'overnight_stay' => 'Post operative stay',
			'site_id' => 'Site',
			'priority_id' => 'Priority',
			'decision_date' => 'Decision date',
			'comments' => 'Add comments',
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
		$criteria->compare('event_id', $this->event_id, true);

		$criteria->compare('eye_id', $this->eye_id);
		$criteria->compare('procedures', $this->procedures);
		$criteria->compare('consultant_required', $this->consultant_required);
		$criteria->compare('anaesthetic_type_id', $this->anaesthetic_type_id);
		$criteria->compare('overnight_stay', $this->overnight_stay);
		$criteria->compare('site_id', $this->site_id);
		$criteria->compare('priority_id', $this->priority_id);
		$criteria->compare('decision_date', $this->decision_date);
		$criteria->compare('comments', $this->comments);
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions() {
		$patient_id = (int) $_REQUEST['patient_id'];
		$firm = Yii::app()->getController()->firm;
		$episode = Episode::getCurrentEpisodeByFirm($patient_id, $firm);
		if($episode && $episode->diagnosis) {
			$this->eye_id = $episode->eye_id;
		}
	}

	public function getproc_defaults() {
		$ids = array();
		foreach (OphTrOperation_Operation_Defaults::model()->findAll() as $item) {
			$ids[] = $item->value_id;
		}
		return $ids;
	}

	protected function beforeSave()
	{
		$anaesthetistRequired = array(
			'LAC','LAS','GA'
		);
		$this->anaesthetist_required = in_array($this->anaesthetic_type->name, $anaesthetistRequired);

		if (!$this->status_id) {
			$this->status_id = 1;
		}

		return parent::beforeSave();
	}

	protected function afterSave()
	{
		if (!empty($_POST['Procedures'])) {

			$existing_ids = array();

			foreach (OphTrOperation_Operation_Procedures::model()->findAll('element_id = :elementId', array(':elementId' => $this->id)) as $item) {
				$existing_ids[] = $item->proc_id;
			}

			foreach ($_POST['Procedures'] as $id) {
				if (!in_array($id,$existing_ids)) {
					$item = new OphTrOperation_Operation_Procedures;
					$item->element_id = $this->id;
					$item->proc_id = $id;

					if (!$item->save()) {
						throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}

			foreach ($existing_ids as $id) {
				if (!in_array($id,$_POST['Procedures'])) {
					$item = OphTrOperation_Operation_Procedures::model()->find('element_id = :elementId and proc_id = :lookupfieldId',array(':elementId' => $this->id, ':lookupfieldId' => $id));
					if (!$item->delete()) {
						throw new Exception('Unable to delete MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}
		}

		return parent::afterSave();
	}

	protected function beforeValidate()
	{
		return parent::beforeValidate();
	}

	protected function afterValidate() {
		if (!empty($_POST['Element_OphTrOperation_Operation']) && empty($_POST['Procedures'])) {
			$this->addError('procedures', 'At least one procedure must be entered');
		}

		return parent::afterValidate();
	}

	public static function getLetterOptions()
	{
		return array(
			'' => 'Any',
			self::LETTER_INVITE => 'Invitation',
			self::LETTER_REMINDER_1 => '1st Reminder',
			self::LETTER_REMINDER_2 => '2nd Reminder',
			self::LETTER_GP => 'Refer to GP'
		);
	}

	public function getLetterType() {
		$letterTypes = $this->getLetterOptions();
		$letterType = ($this->getDueLetter() !== null && isset($letterTypes[$this->getDueLetter()])) ? $letterTypes[$this->getDueLetter()] : false;
		$has_gp = ($this->getDueLetter() != self::LETTER_GP || ($this->event->episode->patient->practice && $this->event->episode->patient->practice->address));
		$patient = $this->event->episode->patient;
		$has_address = (bool) $patient->correspondAddress;

		if ($letterType == false && $this->getLastLetter() == self::LETTER_GP) {
			$letterType = 'Refer to GP';
		}
	}

	public function getLastLetter()
	{
		if (!$this->date_letter_sent) {
			return null;
		}
		if (
			!is_null($this->date_letter_sent->date_invitation_letter_sent) and
			$this->date_letter_sent->date_invitation_letter_sent and	// an invitation letter has been sent
			is_null($this->date_letter_sent->date_1st_reminder_letter_sent) and // but no 1st reminder
			is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and // no 2nd reminder
			is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
		) {
			return self::LETTER_INVITE;
		}
		if (
			$this->date_letter_sent->date_invitation_letter_sent and	// an invitation letter has been sent
			$this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
			is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and // but no 2nd reminder
			is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
		) {
			return self::LETTER_REMINDER_1;
		}
		if (
			$this->date_letter_sent->date_invitation_letter_sent and	// an invitation letter has been sent
			$this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
			$this->date_letter_sent->date_2nd_reminder_letter_sent and // and a 2nd reminder
			is_null($this->date_letter_sent->date_gp_letter_sent) // no gp letter
		) {
			return self::LETTER_REMINDER_2;
		}
		if (
			$this->date_letter_sent->date_invitation_letter_sent and	// an invitation letter has been sent
			$this->date_letter_sent->date_1st_reminder_letter_sent and // and a 1st reminder
			$this->date_letter_sent->date_2nd_reminder_letter_sent and // and a 2nd reminder
			$this->date_letter_sent->date_gp_letter_sent // and a gp letter
		) {
			return self::LETTER_GP;
		}
		return null;
	}

	public function getNextLetter()
	{
		if (is_null($this->getLastLetter())) {
			return self::LETTER_INVITE;
		} else {
			$lastletter = $this->getLastLetter();
			if ($lastletter == self::LETTER_INVITE) {
				return self::LETTER_REMINDER_1;
			} elseif ($lastletter == self::LETTER_REMINDER_1) {
				return self::LETTER_REMINDER_2;
			} elseif ($lastletter == self::LETTER_REMINDER_2) {
				return self::LETTER_GP;
			} elseif ($lastletter == self::LETTER_GP) {
				return self::LETTER_REMOVAL;
			}
		}
	}

	public function getDueLetter()
	{
		$lastletter = $this->getLastLetter();
		if (!$this->getWaitingListStatus()) { // if getwaitingliststatus returns null, we're white
			return $lastletter; // no new letter is due, so we should print the last one
		}
		if ($this->getWaitingListStatus() == self::STATUS_PURPLE) {
			return self::LETTER_INVITE;
		} elseif ($this->getWaitingListStatus() == self::STATUS_GREEN1) {
			return self::LETTER_REMINDER_1;
		} elseif ($this->getWaitingListStatus() == self::STATUS_GREEN2) {
			return self::LETTER_REMINDER_2;
		} elseif ($this->getWaitingListStatus() == self::STATUS_ORANGE) {
			return self::LETTER_GP;
		} elseif ($this->getWaitingListStatus() == self::STATUS_RED) {
			return null; // possibly this should return the gp letter, though it's already been sent?
		} else {
			return null; // possibly this should return $lastletter ?
		}
	}

	/**
	 * Returns the letter status for an operation.
	 *
	 * Checks to see if it's an operation to be scheduled or an operation to be rescheduled. If it's the former it bases its calculation
	 *	 on the operation creation date. If it's the latter it bases it on the most recent cancelled_booking creation date.
		 *
	 * return int
	 */
	public function getWaitingListStatus()
	{
		if (is_null($this->getLastLetter())) {
			return self::STATUS_PURPLE; // no invitation letter has been sent
		} elseif (
			is_null($this->date_letter_sent->date_invitation_letter_sent) and
			is_null($this->date_letter_sent->date_1st_reminder_letter_sent) and
			is_null($this->date_letter_sent->date_2nd_reminder_letter_sent) and
			is_null($this->date_letter_sent->date_gp_letter_sent)
		) {
			return self::STATUS_PURPLE; // no invitation letter has been sent
		}

		$now = new DateTime(); $now->setTime(0,0,0); // $two_weeks_ago = $now->modify('-14 days');
		$now = new DateTime(); $now->setTime(0,0,0); // $one_week_ago = $now->modify('-7 days');

		// if the last letter was the invitation and it was sent over two weeks ago from now:
		$date_sent = new DateTime($this->date_letter_sent->date_invitation_letter_sent); $date_sent->setTime(0,0,0);
		if ( ($this->getLastLetter() == self::LETTER_INVITE) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600) ) {
			return self::STATUS_GREEN1;
		}

		// if the last letter was the 1st reminder and it was sent over two weeks ago from now:
		$date_sent = new DateTime($this->date_letter_sent->date_1st_reminder_letter_sent); $date_sent->setTime(0,0,0);
		if ( ($this->getLastLetter() == self::LETTER_REMINDER_1) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600) ) {
			return self::STATUS_GREEN2;
		}

		// if the last letter was the 2nd reminder and it was sent over two weeks ago from now:
		$date_sent = new DateTime($this->date_letter_sent->date_2nd_reminder_letter_sent); $date_sent->setTime(0,0,0);
		if ( ($this->getLastLetter() == self::LETTER_REMINDER_2) and ($now->getTimestamp() - $date_sent->getTimestamp() > 1209600) ) {
			return self::STATUS_ORANGE;
		}
		// if the last letter was the gp letter and it was sent over one week ago from now:
		$date_sent = new DateTime($this->date_letter_sent->date_gp_letter_sent); $date_sent->setTime(0,0,0);
		if ( ($this->getLastLetter() == self::LETTER_GP) and ($now->getTimestamp() - $date_sent->getTimestamp() > 604800) ) {
			return self::STATUS_RED;
		}
		return null;
	}

	public function getWaitingListLetterStatus()
	{
		echo var_export($this->date_letter_sent,true);
		Yii::app()->end();
	}

	public function getMinDate() {
		$date = strtotime($this->event->datetime);

		if ($this->schedule_timeframe->schedule_options_id != 1) {
			$interval = str_replace('After ', '+', $this->getScheduleText());
			$date = strtotime($interval, $date);
		}

		return $date;
	}

	public function getSchedule_timeframe() {
		return Element_OphTrOperation_ScheduleOperation::model()->find('event_id=?',array($this->event_id));
	}

	public function getSessions($emergency = false)
	{
		$minDate = $this->getMinDate();
		$thisMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
		if ($minDate < $thisMonth) {
			$minDate = $thisMonth;
		}

		$monthStart = empty($_GET['date']) ? date('Y-m-01', $minDate) : $_GET['date'];

		if (!$emergency) {
			$firmId = empty($_GET['firm']) ? $this->event->episode->firm_id : $_GET['firm'];
		} else {
			$firmId = null;
		}

		$sessions = OphTrOperation_Operation_Session::get($monthStart, $minDate, $firmId);

		$results = array();
		foreach ($sessions as $session) {
			$date = $session['date'];
			$weekday = date('N', strtotime($date));
			$text = $this->getWeekdayText($weekday);

			$sessionTime = explode(':', $session['session_duration']);
			$session['duration'] = ($sessionTime[0] * 60) + $sessionTime[1];
			$session['time_available'] = $session['duration'] - $session['bookings_duration'];
			unset($session['session_duration'], $session['date']);

			$results[$text][$date]['sessions'][] = $session;
		}

		foreach ($results as $weekday => $dates) {
			$timestamp = strtotime($monthStart);
			$firstWeekday = strtotime(date('Y-m-t', $timestamp - (60 * 60 * 24)));
			$dateList = array_keys($dates);
			while (date('N', strtotime($dateList[0])) != date('N', $firstWeekday)) {
				$firstWeekday -= 60 * 60 * 24;
			}

			for ($weekCounter = 1; $weekCounter < 8; $weekCounter++) {
				$addDays = ($weekCounter - 1) * 7;
				$selectedDay = date('Y-m-d', mktime(0, 0, 0, date('m', $firstWeekday), date('d', $firstWeekday) + $addDays, date('Y', $firstWeekday)));
				if (in_array($selectedDay, $dateList)) {
					foreach ($dates[$selectedDay] as $sessions) {
						$totalSessions = count($sessions);
						$status = $totalSessions;

						$open = $full = 0;

						foreach ($sessions as $session) {
							if ($session['time_available'] >= $this->total_duration) {
								$open++;
							} else {
								$full++;
							}
						}
						if ($full == $totalSessions) {
							$status = 'full';
						} elseif ($full > 0 && $open > 0) {
							$status = 'limited';
						} elseif ($open == $totalSessions) {
							$status = 'available';
						}
					}
				} else {
					$status = 'closed';
				}
				$results[$weekday][$selectedDay]['status'] = $status;
			}
		}

		foreach ($results as $weekday => &$dates) {
			$dateSort = array();
			foreach ($dates as $date => $info) {
				$dateSort[] = $date;
			}

			array_multisort($dateSort, SORT_ASC, $dates);
		}

		return $results;
	}
}
?>
