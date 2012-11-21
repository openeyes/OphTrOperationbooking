<?php

class DefaultController extends BaseEventTypeController {
	public function actionCreate() {
		if (@$_POST['schedule_now']) {
			$this->successUri = 'booking/schedule/';
		}

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

	public function actionCancel($id) {
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		} 

		Yii::app()->clientScript->registerCSSFile(Yii::app()->createUrl('css/theatre_calendar.css'), 'all');

		$errors = array();

		if (isset($_POST['cancellation_reason']) && isset($_POST['operation_id'])) {
			if (!$operation = Element_OphTrOperation_Operation::model()->findByPk($_POST['operation_id'])) {
				throw new CHttpException(500,'Operation not found');
			}
			
			$comment = (isset($_POST['cancellation_comment'])) ? strip_tags(@$_POST['cancellation_comment']) : '';
			$result = $operation->cancel(@$_POST['cancellation_reason'], $comment);

			if ($result['result']) {
				$operation->event->deleteIssues();
		
				$event->audit('event','cancel',false);

				die(json_encode(array()));
			}

			foreach ($result['errors'] as $form_errors) {
				foreach ($form_errors as $error) {
					$errors[] = $error;
				}
			}

			die(json_encode($errors));
		}

		if (!$operation = Element_OphTrOperation_Operation::model()->find('event_id=?',array($id))) {
			throw new CHttpException(500,'Operation not found');
		}

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Cancel operation';

		$this->renderPartial('cancel', array(
				'operation' => $operation,
				'patient' => $operation->event->episode->patient,
				'date' => $operation->minDate,
				'errors' => $errors
			), false, true);
	}
}
