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

class DefaultController extends BaseEventTypeController
{
	public $eventIssueCreate = 'Operation requires scheduling';

	protected function beforeAction($action)
	{
		$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/booking.js');
		Yii::app()->clientScript->registerScriptFile('/js/jquery.validate.min.js');
		Yii::app()->clientScript->registerScriptFile('/js/additional-validators.js');

		return parent::beforeAction($action);
	}

	public function actionCreate()
	{
		if (@$_POST['schedule_now']) {
			$this->successUri = 'booking/schedule/';
		}

		parent::actionCreate();
	}

	public function actionUpdate($id)
	{
		parent::actionUpdate($id);
	}

	public function actionView($id)
	{
		$this->extraViewProperties = array(
			'operation' => Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($id)),
		);

		parent::actionView($id);
	}

	public function actionPrint($id)
	{
		parent::actionPrint($id);
	}

	public function printActions()
	{
		return array('print','admissionLetter');
	}

	public function actionCancel($id)
	{
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Unable to find event: '.$id);
		}

		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($event->id))) {
			throw new CHttpException(500,'Operation not found');
		}

		if ($operation->status->name == 'Cancelled') {
			return $this->redirect(array('default/view/'.$event->id));
		}

		$errors = array();

		if (isset($_POST['cancellation_reason']) && isset($_POST['operation_id'])) {
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

		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($id))) {
			throw new CHttpException(500,'Operation not found');
		}

		$this->patient = $operation->event->episode->patient;
		$this->title = 'Cancel operation';

		$this->processJsVars();

		$this->render('cancel', array(
			'operation' => $operation,
			'patient' => $operation->event->episode->patient,
			'date' => $operation->minDate,
			'errors' => $errors
		));
	}

	public function actionAdmissionLetter($id)
	{
		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception('Event not found: '.$id);
		}

		$this->layout = '//layouts/pdf';

		if ($event->episode->patient->date_of_death) {
			return false;
		}

		if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id = ?',array($id))) {
			throw new Exception('Operation not found for event: '.$id);
		}

		$event->audit('admission letter','print',false);

		$this->logActivity('printed admission letter');

		$site = $operation->booking->session->theatre->site;
		if (!$firm = $operation->booking->session->firm) {
			$firm = $operation->event->episode->firm;
			$emergency_list = true;
		}
		$emergency_list = false;

		$pdf_print = new OEPDFPrint('Openeyes', 'Booking letters', 'Booking letters');

		$body = $this->render('../letters/admission_letter', array(
			'site' => $site,
			'patient' => $event->episode->patient,
			'firm' => $firm,
			'emergencyList' => $emergency_list,
			'operation' => $operation,
		), true);

		$oeletter = new OELetter(
			$event->episode->patient->getLetterAddress(array(
				'include_name' => true,
				'delimiter' => "\n",
			)),
			$site->getLetterAddress(array(
				'include_name' => true,
				'include_telephone' => true,
				'include_fax' => true,
				'delimiter' => "\n",
			))
		);

		$oeletter->setBarcode('E:'.$operation->event_id);
		$oeletter->addBody($body);

		$pdf_print->addLetter($oeletter);

		$body = $this->render('../letters/admission_form', array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $event->episode->patient,
				'firm' => $firm,
				'emergencyList' => $emergency_list,
		), true);

		$oeletter = new OELetter;
		$oeletter->setFont('helvetica','10');
		$oeletter->setBarcode('E:'.$operation->event_id);
		$oeletter->addBody($body);

		$pdf_print->addLetter($oeletter);
		$pdf_print->output();
	}
}
