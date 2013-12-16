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
	protected $operation_required = false;
	/** @var Element_OphTrOperation_Operation $operation */
	protected $operation = null;

	protected function beforeAction($action)
	{
		Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/booking.js');
		Yii::app()->clientScript->registerScriptFile('/js/jquery.validate.min.js');
		Yii::app()->clientScript->registerScriptFile('/js/additional-validators.js');
		return parent::beforeAction($action);
	}

	/**
	 * Various default options for operation should be driven by the episode
	 *
	 * @param BaseEventTypeElement $element
	 * @param string $action
	 */
	protected function setElementDefaultOptions($element, $action)
	{
		parent::setElementDefaultOptions($element, $action);
		if ($action == 'create') {
			$kls = get_class($element);
			if ($kls == 'Element_OphTrOperationbooking_Diagnosis') {
				// set default eye and disorder
				if ($this->episode && $this->episode->diagnosis) {
					$element->eye_id = $this->episode->eye_id;
					$element->disorder_id = $this->episode->disorder_id;
				}
			}
			elseif ($kls == 'Element_OphTrOperationbooking_Operation') {
				// set the default eye
				if ($this->episode && $this->episode->diagnosis) {
					$element->eye_id = $this->episode->eye_id;
				}

				// set default anaesthetic based on whether patient is a child or not.
				$key = $this->patient->isChild() ? 'ophtroperationbooking_default_anaesthetic_child' : 'ophtroperationbooking_default_anaesthetic';

				if (isset(Yii::app()->params[$key])) {
					if ($at = AnaestheticType::model()->find('code=?',array(Yii::app()->params[$key]))) {
						$element->anaesthetic_type_id = $at->id;
					}
				}
			}
		}
	}

	/**
	 * Sets up operation based on the event
	 *
	 * @param $id
	 * @throws CHttpException
	 * (non-phpdoc)
	 * @see BaseEventTypeController::initWithEventId($id)
	 */
	protected function initWithEventId($id)
	{
		parent::initWithEventId($id);

		$this->operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($this->event->id));
		if ($this->operation_required && !$this->operation) {
			throw new CHttpException(500,'Operation not found');
		}
	}

	/**
	 * Checks whether schedule now has been requested
	 *
	 * (non-phpdoc)
	 * @see BaseEventTypeController::initActionCreate()
	 */
	protected function initActionCreate()
	{
		parent::initActionCreate();
		if (@$_POST['schedule_now']) {
			$this->successUri = 'booking/schedule/';
		}
	}

	/**
	 * Make the operation element directly available for templates
	 *
	 * @see BaseEventTypeController::initActionView()
	 */
	public function initActionView()
	{
		$this->operation_required = true;
		parent::initActionView();

		$this->extraViewProperties = array(
			'operation' => $this->operation,
		);
	}

	/**
	 * @see BaseEventTypeController::setElementComplexAttributesFromData($element, $data, $index)
	 */
	protected function setElementComplexAttributesFromData($element, $data, $index = null)
	{
		// Using the ProcedureSelection widget, so the field doesn't map directly to the element attribute
		if (get_class($element) == 'Element_OphTrOperationbooking_Operation') {
			if (isset($data['Element_OphTrOperationbooking_Operation']['total_duration_procs'])) {
				$element->total_duration = $data['Element_OphTrOperationbooking_Operation']['total_duration_procs'];
			}
			$procs = array();
			if (isset($data['Procedures_procs'])) {
				foreach ($data['Procedures_procs'] as $proc_id) {
					$procs[] = Procedure::model()->findByPk($proc_id);
				}
			}
			$element->procedures = $procs;
		}
	}

	/**
	 * Set procedures for Element_OphTrOperationbooking_Operation
	 *
	 * @param $data
	 */
	protected function saveEventComplexAttributesFromData($data)
	{
		foreach ($this->open_elements as $element) {
			if (get_class($element) == 'Element_OphTrOperationbooking_Operation') {
				// using the ProcedureSelection widget, so not a direct field on the operation element
				$element->updateProcedures(isset($data['Procedures_procs']) ? $data['Procedures_procs'] : array());
			}
		}
	}

	/**
	 * Extend standard behaviour to perform validation of elements across the event
	 * 
	 * @param array $data
	 * @return array
	 */
	protected function setAndValidateElementsFromData($data)
	{
		$errors = parent::setAndValidateElementsFromData($data);
		// need to do some validation at the event level

		$event_errors = OphTrOperationbooking_BookingHelper::validateElementsForEvent($this->open_elements);
		if ($event_errors) {
			if ($errors['Event']) {
				$errors['Event'] = array_merge($errors['Event'], $event_errors);
			}
			else {
				$errors['Event'] = $event_errors;
			}
		}

		return $errors;
	}

	/**
	 * @return array
	 * @see BaseEventTypeController::printActions()
	 */
	public function printActions()
	{
		return array('print','admissionLetter');
	}

	/**
	 * Setup event properties
	 */
	protected function initActionCancel()
	{
		$this->operation_required = true;
		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * Cancel operation action
	 *
	 * @param $id
	 * @throws CHttpException
	 * @throws Exception
	 */
	public function actionCancel($id)
	{

		$operation = $this->operation;

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

	/**
	 * Setup event properties
	 */
	protected function initActionAdmissionLetter()
	{
		$this->operation_required = true;
		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * Generate admission letter for operation booking
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function actionAdmissionLetter()
	{
		$this->layout = '//layouts/pdf';

		if ($this->patient->date_of_death) {
			// no admission for dead patients
			return false;
		}

		$operation = $this->operation;

		$this->event->audit('admission letter','print',false);

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
			'patient' => $this->event->episode->patient,
			'firm' => $firm,
			'emergencyList' => $emergency_list,
			'operation' => $operation,
		), true);

		$oeletter = new OELetter(
			$this->event->episode->patient->getLetterAddress(array(
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
				'patient' => $this->event->episode->patient,
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
