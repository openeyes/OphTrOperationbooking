<?php

class BookingController extends BaseEventTypeController {
	public $reschedule = false;
	public $js = array(
		'js/jquery.validate.min.js',
		'js/additional-validators.js',
	);

	public function actionSchedule($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		if (!$this->title) {
			$this->title = "Schedule operation";
		}

		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($id))) {
			throw new Exception('Operation not found');
		}

		if ($operation->status->name == 'Cancelled') {
			return $this->redirect(array('default/view/'.$event->id));
		}

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

			if ($session = OphTrOperationbooking_Operation_Session::model()->findByPk(@$_GET['session_id'])) {
				$criteria = new CDbCriteria;
				$criteria->compare('session_id', $session->id);
				$criteria->addCondition('cancellation_date is null');
				$criteria->order = 'display_order ASC';
				$bookings = OphTrOperationbooking_Operation_Booking::model()->findAll($criteria);

				foreach ($theatres as $name => $list) {
					foreach ($list as $theatre) {
						if ($theatre['session_id'] == $session->id) {
							$bookable = $theatre['bookable'];
						}
					}
				}

				if (!empty($_POST['Booking']['element_id'])) {
					if (!$operation = Element_OphTrOperationbooking_Operation::model()->findByPk($_POST['Booking']['element_id'])) {
						throw new Exception('Operation not found: '.$_POST['Booking']['element_id']);
					}

					if (($result = $operation->schedule($_POST['Booking'], $_POST['Operation']['comments'], $_POST['Session']['comments'], $this->reschedule)) !== true) {
						$errors = $result;
					} else {
						$this->redirect(array('/OphTrOperationbooking/default/view/'.$operation->event_id));
					}
				} else {
					$_POST['Booking']['admission_time'] = ($session['start_time'] == '13:30:00') ? '12:00' : date('H:i', strtotime('-1 hour', strtotime($session['start_time'])));
					$_POST['Session']['comments'] = $session['comments'];
					$_POST['Operation']['comments'] = $operation->comments;
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
				'errors' => @$errors,
				), false, true);
	}

	public function actionReschedule($id) {
		$this->title = "Reschedule operation";
		$this->reschedule = true;
		return $this->actionSchedule($id);
	}

	public function actionRescheduleLater($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($id))) {
			throw new Exception('Operation not found');
		}

		if (in_array($operation->status->name,array('Requires scheduling','Requires rescheduling','Cancelled'))) {
			return $this->redirect(array('default/view/'.$event->id));
		}

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Reschedule later';

		Yii::app()->clientScript->registerCSSFile(Yii::app()->createUrl('css/theatre_calendar.css'), 'all');

		if (!empty($_POST)) {
			if (!$reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->findByPk($_POST['cancellation_reason'])) {
				$errors = array("Please select a rescheduling reason");
			} else if (isset($_POST['booking_id'])) {
				if (!$booking = OphTrOperationbooking_Operation_Booking::model()->findByPk($_POST['booking_id'])) {
					throw new Exception('Booking not found: '.@$_POST['booking_id']);
				}

				$booking->cancel($reason,$_POST['cancellation_comment'],false);
				$operation->setStatus('Requires rescheduling');

				$this->redirect(array('/OphTrOperationbooking/default/view/'.$event->id));
			}
		}

		$this->renderPartial('reschedule_later', array(
				'operation' => $operation,
				'date' => $operation->minDate,
				'patient' => $operation->event->episode->patient,
				'errors' => @$errors,
			),
			false,
			true
		);
	}
}
