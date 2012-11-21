<?php

class BookingController extends BaseEventTypeController {
	public function actionCreate() {
		$booking = new OphTrOperation_Operation_Booking;

		if (isset($_POST['Booking'])) {
			// This is enforced in the model so no need to if ()
			preg_match('/(^[0-9]{1,2}).*?([0-9]{2})$/',$_POST['Booking']['admission_time'],$m);
			$_POST['Booking']['admission_time'] = $m[1].":".$m[2];

			$booking->attributes=$_POST['Booking'];

			$session = OphTrOperation_Operation_Session::model()->findByPk($booking->session_id);

			$operation = Element_OphTrOperation_Operation::model()->findByPk($booking->element_id);

			if (!empty($operation->booking)) {
				// This operation already has a booking. There must be two users creating an episode at once
				//	or suchlike. Ignore and return.
				$this->redirect(Yii::app()->createUrl('/'.$operation->event->eventType->class_name.'/default/view/'.$operation->event_id));
				return;
			}

			$booking->session_date = $session->date;
			$booking->session_start_time = $session->start_time;
			$booking->session_end_time = $session->end_time;
			$booking->session_theatre_id = $session->theatre_id;

			if (!empty($_POST['wardType'])) {
				/* currently not in use, but if we want to allow a checkbox for
				 * booking into an observational ward, it would be handled here
				 */
				$observationWard = OphTrOperation_Operation_Ward::model()->findByAttributes(
					array('site_id' => $session->theatre->site_id,
						'restriction' => OphTrOperation_Operation_Ward::RESTRICTION_OBSERVATION));
				if (!empty($observationWard)) {
					$booking->ward_id = $observationWard->id;
				} else {
					$wards = $operation->getWardOptions(
						$session->theatre->site_id, $session->theatre_id);
					$booking->ward_id = key($wards);
				}
			} elseif (!empty($operation) && !empty($session)) {
				$wards = $operation->getWardOptions(
					$session->theatre->site_id, $session->theatre_id);
				$booking->ward_id = key($wards);
			}

			$booking->display_order = 1;

			if (!empty($_POST['Booking']['session_id'])) {
				$criteria = new CDbCriteria;
				$criteria->condition='session_id = :id';
				$criteria->params = array(':id' => $_POST['Booking']['session_id']);
				$criteria->order = 'display_order DESC';
				$criteria->limit = 1;
				if ($booking2 = OphTrOperation_Operation_Booking::model()->find($criteria)) {
					$booking->display_order = $booking2->display_order + 1;
				}
			}

			if ($booking->save()) {
				if (!$operation->erod) {
					$operation->calculateEROD($session->id);
				}

				OELog::log("Booking made $booking->id");

				$audit = new Audit;
				$audit->action = "create";
				$audit->target_type = "booking";
				$audit->patient_id = $operation->event->episode->patient->id;
				$audit->episode_id = $operation->event->episode_id;
				$audit->event_id = $operation->event_id;
				$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
				$audit->data = $booking->getAuditAttributes();
				$audit->save();
				// Update episode status to 'listed'
				$operation->event->episode->episode_status_id = 3;
				if (!$operation->event->episode->save()) {
					throw new Exception('Unable to change episode status id for episode '.$operation->event->episode->id);
				}

				$operation->event->deleteIssues();

				if (Yii::app()->params['urgent_booking_notify_hours'] && Yii::app()->params['urgent_booking_notify_email']) {
					if (strtotime($session->date) <= (strtotime(date('Y-m-d')) + (Yii::app()->params['urgent_booking_notify_hours'] * 3600))) {
						if (!is_array(Yii::app()->params['urgent_booking_notify_email'])) {
							$targets = array(Yii::app()->params['urgent_booking_notify_email']);
						} else {
							$targets = Yii::app()->params['urgent_booking_notify_email'];
						}
						foreach ($targets as $email) {
							mail($email, "[OpenEyes] Urgent booking made","A patient booking was made with a TCI date within the next 24 hours.\n\nDisorder: ".$operation->getDisorder()."\n\nPlease see: http://".@$_SERVER['SERVER_NAME']."/transport\n\nIf you need any assistance you can reply to this email and one of the OpenEyes support personnel will respond.","From: ".Yii::app()->params['urgent_booking_notify_email_from']."\r\n");
						}
					}
				}

				if ($operation->status->name == 'Needs rescheduling') {
					$operation->status_id = OphTrOperation_Operation_Status::model()->find('name=?',array('Rescheduled'))->id;
				} else {
					$operation->status_id = OphTrOperation_Operation_Status::model()->find('name=?',array('Scheduled'))->id;
				}
				if (!empty($_POST['Operation']['comments'])) {
					$operation->comments = $_POST['Operation']['comments'];
				}

				// Update the proposed site for the operation to match the booked site (ward)
				// This gives better information on the waiting list when it comes to rescheduling
				$operation->site_id = $booking->ward->site_id;

				if (!$operation->save()) {
					throw new SystemException('Unable to update operation data: '.print_r($operation->getErrors(),true));
				}

				if (!empty($_POST['Session']['comments'])) {
					$session->comments = $_POST['Session']['comments'];
					if (!$session->save()) {
						throw new SystemException('Unable to save session comments: '.print_r($session->getErrors(),true));
					}
				}

				$patientId = $booking->operation->event->episode->patient->id;

				die(json_encode(array()));
			} else {
				throw new Exception('Unable to save booking: '.print_r($booking->getErrors(),true));
			}
		}
	}

	public function actionUpdate($id) {
	}

	public function actionSchedule($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id));

		$this->patient = $event->episode->patient;

		if (@$_GET['firmId']) {
			if (!$firm = Firm::model()->findByPk(@$_GET['firmId'])) {
				throw new Exception('Unknown firm id: '.$_GET['firmId']);
			}
		} else {
			$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		}

		$this->renderPartial(
			'schedule', array(
			'event' => $event,
			'firm' => $firm,
			'firmList' => $firmList = Firm::model()->getListWithSpecialties(),
			'sessions' => $operation->getSessions($firm),
			'date' => $operation->minDate,
			), false, true);
	}

	public function actionSessions() {
		if (!$operation = Element_OphTrOperation_Operation::model()->findByPk(@$_GET['operation'])) {
			throw new Exception('Operation id is invalid.');
		}

		$minDate = !empty($_GET['date']) ? strtotime($_GET['date']) : $operation->getMinDate();
		$firmId = empty($_GET['firmId']) ? $operation->event->episode->firm_id : $_GET['firmId'];

		if ($firmId != 'EMG') {
			$_GET['firm'] = $firmId;
			$firm = Firm::model()->findByPk($firmId);
			$siteList = Site::getListByFirm($firmId);
		} else {
			$firm = new Firm;
			$firm->name = 'Emergency List';
			$siteList = Site::model()->getList();
		}

		$siteId = !empty($_GET['siteId']) ? $_GET['siteId'] : key($siteList);
		$sessions = !empty($siteId) ? $operation->getSessions($firm, $siteId) : array();

		$this->renderPartial('_calendar', array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions, 'firmId'=>$firmId), false, true);
	}

	public function actionTheatres() {
		if (!$operation = Element_OphTrOperation_Operation::model()->findByPk(@$_GET['operation'])) {
			throw new Exception('Operation id is invalid.');
		}
		if (!@$_GET['month']) throw new Exception('Month is required');
		if (!@$_GET['day']) throw new Exception('Day is required');

		$firmId = empty($_GET['firm']) ? 'EMG' : $_GET['firm'];
		$reschedule = !(empty($_REQUEST['reschedule']) || $_REQUEST['reschedule'] == 0);

		$operation->getMinDate();

		$time = strtotime($_GET['month']);
		$date = date('Y-m-d', mktime(0,0,0,date('m', $time), $_GET['day'], date('Y', $time)));
		$theatres = $operation->getTheatres($date, $firmId);

		$this->renderPartial('_theatre_times', array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres, 'reschedule' => $reschedule), false, true);
	}

	public function actionList() {
		if (!$operation = Element_OphTrOperation_Operation::model()->findByPk(@$_GET['operation'])) {
			throw new Exception('Operation id is invalid.');
		}
		if (!$session = OphTrOperation_Operation_Session::model()->findByPk(@$_GET['session'])) {
			throw new Exception('Session id is invalid.');
		}

		$criteria = new CDbCriteria;
		$criteria->compare('session_id', $session->id);
		$criteria->order = 'display_order ASC';
		$bookings = Booking::model()->findAll($criteria);

		$reschedule = !(empty($_REQUEST['reschedule']) || $_REQUEST['reschedule'] == 0);

		$this->renderPartial('_list', array('operation'=>$operation, 'session'=>$session, 'bookings'=>$bookings, 'reschedule'=>$reschedule), false, true);
	}
}
