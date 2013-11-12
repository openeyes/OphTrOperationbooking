<?php
/**
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

class BookingController extends BaseEventTypeController
{
	public $reschedule = false;

	protected function beforeAction($action)
	{
		$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/booking.js');
		Yii::app()->clientScript->registerScriptFile('/js/jquery.validate.min.js');
		Yii::app()->clientScript->registerScriptFile('/js/additional-validators.js');

		return parent::beforeAction($action);
	}

	public function accessRules()
	{
		return array(
			// Level 3 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(4)',
			),
			array('deny'),
		);
	}

	public function actionSchedule($id)
	{
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
				$criteria->addCondition('`t`.booking_cancellation_date is null');
				$criteria->addCondition('event.deleted = 0');
				$criteria->order = 'display_order ASC';
				$bookings = OphTrOperationbooking_Operation_Booking::model()->with(array('operation'=>array('with'=>'event')))->findAll($criteria);

				foreach ($theatres as $theatre) {
					foreach ($theatre->sessions as $_session) {
						if ($session->id == $_session->id) {
							$bookable = $_session->operationBookable($operation);
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
						$this->redirect(array('default/view/'.$operation->event_id));
					}
				} else {
					$_POST['Booking']['admission_time'] = ($session['start_time'] == '13:30:00') ? '12:00' : date('H:i', strtotime('-1 hour', strtotime($session['start_time'])));
					$_POST['Session']['comments'] = $session['comments'];
					$_POST['Operation']['comments'] = $operation->comments;
				}
			}
		} elseif ($operation->booking) {
			$selectedDate = $operation->booking->session->date;
		}

		$this->processJsVars();

		$this->render('schedule', array(
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
		));
	}

	public function actionReschedule($id)
	{
		$this->title = "Reschedule operation";
		$this->reschedule = true;
		return $this->actionSchedule($id);
	}

	public function actionRescheduleLater($id)
	{
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

		$errors = array();

		if (!empty($_POST)) {
			if (strlen($_POST['cancellation_comment']) >200) {
				$errors[] = "Comments must be 200 characters max";
			}
			if (!$reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->findByPk($_POST['cancellation_reason'])) {
				$errors[] = "Please select a rescheduling reason";
			} elseif (isset($_POST['booking_id']) && empty($errors)) {
				if (!$booking = OphTrOperationbooking_Operation_Booking::model()->findByPk($_POST['booking_id'])) {
					throw new Exception('Booking not found: '.@$_POST['booking_id']);
				}

				$booking->cancel($reason,$_POST['cancellation_comment'],false);
				$operation->setStatus('Requires rescheduling');

				$this->redirect(array('default/view/'.$event->id));
			}
		}

		$this->processJsVars();

		$this->render('reschedule_later', array(
			'operation' => $operation,
			'date' => $operation->minDate,
			'patient' => $operation->event->episode->patient,
			'errors' => $errors,
		));
	}
}
