<?php

class DefaultController extends BaseEventTypeController {
	public function actionCreate() {
		parent::actionCreate();
	}

	public function actionUpdate($id) {
		parent::actionUpdate($id);
	}

	public function actionView($id) {
		parent::actionView($id);
	}

	public function actionPrint($id) {
		parent::actionPrint($id);
	}

	public function actionSchedule($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id));

		$this->patient = $event->episode->patient;

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		$this->renderPartial(
			'schedule', array(
			'event' => $event,
			'firm' => $firm,
			'firmList' => $firmList = Firm::model()->getListWithSpecialties(),
			'sessions' => $operation->getSessions($firm->name == 'Emergency List'),
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
		$sessions = !empty($siteId) ? $operation->getSessions($firm->name == 'Emergency List', $siteId) : array();

		$this->renderPartial('_calendar', array('operation'=>$operation, 'date'=>$minDate, 'sessions'=>$sessions, 'firmId'=>$firmId), false, true);
	}

	public function actionTheatres() {
		if (!$operation = Element_OphTrOperation_Operation::model()->findByPk(@$_GET['operation'])) {
			throw new Exception('Operation id is invalid.');
		}
		if (!@$_GET['month']) throw new Exception('Month is required');
		if (!@$_GET['day']) throw new Exception('Day is required');

		$firmId = empty($_GET['firm']) ? 'EMG' : $_GET['firm'];
		$reschedule = (empty($_REQUEST['reschedule']) || $_REQUEST['reschedule'] == 0);

		$operation->getMinDate();

		$time = strtotime($_GET['month']);
		$date = date('Y-m-d', mktime(0,0,0,date('m', $time), $_GET['day'], date('Y', $time)));
		$theatres = $operation->getTheatres($date, $firmId);

		$this->renderPartial('_theatre_times', array('operation'=>$operation, 'date'=>$date, 'theatres'=>$theatres, 'reschedule' => $reschedule), false, true);
	}
}
