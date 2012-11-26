<?php

class BookingController extends BaseEventTypeController {
	public function actionCreate() {
		$BOoking = new OphTrOperation_Operation_Booking;

		if (isset($_POST['Booking'])) {
			if (!$operation = Element_OphTrOperation_Operation::model()->findByPk(@$_POST['Booking']['element_id'])) {
				throw new Exception('Operation not found: '.@$_POST['Booking']['element_id']);
			}

			$operation->schedule($_POST['Booking'], $_POST['Operation']['comments'], $_POST['Session']['comments']);

			die(json_encode(array()));
		}
	}

	public function actionUpdate($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		if (!$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id))) {
			throw new Exception('Unable to find operation: '.$id);
		}

		if (!$reason = OphTrOperation_Operation_Cancellation_Reason::model()->findByPk($_POST['cancellation_reason'])) {
			die(json_encode(array(array("Please select a cancellation reason"))));
		}

		if (isset($_POST['booking_id'])) {
			if (!$booking = OphTrOperation_Operation_Booking::model()->findByPk($_POST['booking_id'])) {
				throw new Exception('Booking not found: '.@$_POST['booking_id']);
			}

			$booking->cancel($reason,$_POST['cancellation_comment'],@$_POST['Booking']);

			if (!empty($_POST['Booking'])) {
				$operation->schedule($_POST['Booking'], $_POST['Operation']['comments'], $_POST['Session']['comments'], true);
			} else {
				$operation->setStatus('Requires rescheduling');
			}

			die(json_encode(array()));
		}
	}

	public function actionSchedule($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		if (!$this->title) {
			$this->title = "Schedule operation";
		}

		$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id));

		$this->patient = $event->episode->patient;

		if (@$_GET['firm_id']) {
			if ($_GET['firm_id'] == 'EMG') {
				$firm = new Firm;
				$firm->name = 'Emergency List';
			} else {
				if (!$firm = Firm::model()->findByPk(@$_GET['firm_id'])) {
					throw new Exception('Unknown firm id: '.$_GET['firm_id']);
				}
			}
		} else {
			$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		}

		if (preg_match('/^([0-9]{4})([0-9]{2})$/',@$_GET['date'],$m)) {
			$date = mktime(0,0,0,$m[2],1,$m[1]);
		} else {
			$date = $operation->minDate;
		}

		if (ctype_digit(@$_GET['day'])) {
			$selectedDate = date('Y-m-d', mktime(0,0,0,date('m', $date), $_GET['day'], date('Y', $date)));
			$theatres = $operation->getTheatres($selectedDate, $firm->id);

			if ($session = OphTrOperation_Operation_Session::model()->findByPk(@$_GET['session_id'])) {
				$criteria = new CDbCriteria;
				$criteria->compare('session_id', $session->id);
				$criteria->order = 'display_order ASC';
				$bookings = OphTrOperation_Operation_Booking::model()->findAll($criteria);

				foreach ($theatres as $name => $list) {
					foreach ($list as $theatre) {
						if ($theatre['session_id'] == $session->id) {
							$bookable = $theatre['bookable'];
						}
					}
				}
			}
		} else if ($operation->booking) {
			$selectedDate = $operation->booking->session->date;
		}

		$this->renderPartial('schedule', array(
				'event' => $event,
				'operation' => $operation,
				'firm' => $firm,
				'firmList' => Firm::model()->listWithSpecialties,
				'date' => $date,
				'selectedDate' => @$selectedDate,
				'sessions' => $operation->getFirmCalendarForMonth($firm, $date),
				'theatres' => @$theatres,
				'session' => @$session,
				'bookings' => @$bookings,
				'bookable' => @$bookable,
				'inthepast' => @$inthepast,
				), false, true);
	}

	public function actionReschedule($id) {
		$this->title = "Reschedule operation";
		return $this->actionSchedule($id);
	}

	public function actionRescheduleLater($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id));

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Reschedule later';

		Yii::app()->clientScript->registerCSSFile(Yii::app()->createUrl('css/theatre_calendar.css'), 'all');

		$this->renderPartial('reschedule_later', array(
				'operation' => $operation,
				'date' => $operation->minDate,
				'patient' => $operation->event->episode->patient,
			),
			false,
			true
		);
	}

	public function init() {
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.validate.min.js'));
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/additional-validators.js'));

		parent::init();
	}
}
