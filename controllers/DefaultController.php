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
}
