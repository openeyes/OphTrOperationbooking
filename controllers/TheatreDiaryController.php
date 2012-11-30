<?php
/**
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

class TheatreDiaryController extends BaseEventTypeController
{
	public $layout='//layouts/main';
 
	public function init() {
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.validate.min.js'));
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/additional-validators.js'));

		parent::init();
	}

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	public function actionIndex()
	{
		$firm = Firm::model()->findByPk($this->selectedFirmId);

		if (empty($firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		$theatres = array();
		$wards = array();

		if (empty($_POST)) {
			// look for values from the session
			$theatre_searchoptions = Yii::app()->session['theatre_searchoptions'];

			if (!empty($theatre_searchoptions)) {
				foreach (Yii::app()->session['theatre_searchoptions'] as $key => $value) {
					$_POST[$key] = $value;
				}

				if (isset($_POST['site-id'])) {
					$wards = $this->getFilteredWards($_POST['site-id']);
					$theatres = $this->getFilteredTheatres($_POST['site-id']);
				}

				if (!isset($_POST['firm-id'])) {
					$_POST['firm-id'] = $theatre_searchoptions['firm-id'] = Yii::app()->session['selected_firm_id'];
					$_POST['subspecialty-id'] = $theatre_searchoptions['subspecialty-id'] = $firm->serviceSubspecialtyAssignment->subspecialty_id;
				}

				Yii::app()->session['theatre_searchoptions'] = $theatre_searchoptions;

			} else {
				$_POST = Yii::app()->session['theatre_searchoptions'] = array(
					'firm-id' => Yii::app()->session['selected_firm_id'],
					'subspecialty-id' => $firm->serviceSubspecialtyAssignment->subspecialty_id
				);

				Yii::app()->session['theatre_searchoptions'] = $_POST;
			}

			Audit::add('diary','view');
		} else {
			Audit::add('diary','search',serialize($_POST));
		}

		$this->render('index', array('wards'=>$wards, 'theatres'=>$theatres));
	}

	public function actionPrintDiary()
	{
		Audit::add('diary','print',serialize($_POST));

		$this->renderPartial('_print_diary', array('theatres'=>$this->getTheatres()), false, true);
	}

	public function actionPrintList() {
		Audit::add('diary','print list',serialize($_POST));

		$this->renderPartial('_print_list', array('bookings'=>$this->getBookingList()), false, true);
	}

	public function actionSearch()
	{
		Audit::add('diary','search',serialize($_POST));

		$this->renderPartial('_list', array('diary' => $this->getDiary()), false, true);
	}

	public function getDiary() {
		$firmId = Yii::app()->session['selected_firm_id'];

		$_POST['date-start'] = Helper::convertNHS2MySQL($_POST['date-start']);
		$_POST['date-end'] = Helper::convertNHS2MySQL($_POST['date-end']);

		if (empty($_POST['date-start']) || empty($_POST['date-end'])) {
			$startDate = $this->getNextSessionDate($firmId);
			$endDate = $startDate;
		} else {
			$startDate = $_POST['date-start'];
			$endDate = $_POST['date-end'];
		}

		$whereSql = 's.date BETWEEN :start AND :end';
		$whereParams = array(':start' => $startDate, ':end' => $endDate);

		if (@$_POST['emergency_list']) {
			$whereSql .= ' and f.id is null';
		} else {
			if (@$_POST['site-id']) {
				$whereSql .= ' AND t.site_id = :siteId';
				$whereParams[':siteId'] = $_POST['site-id'];
			}
			if (@$_POST['theatre-id']) {
				$whereSql .= ' AND t.id = :theatreId';
				$whereParams[':theatreId'] = $_POST['theatre-id'];
			}
			if (@$_POST['subspecialty-id']) {
				$whereSql .= ' AND spec.id = :subspecialtyId';
				$whereParams[':subspecialtyId'] = $_POST['subspecialty-id'];
			}
			if (@$_POST['firm-id']) {
				$whereSql .= ' AND f.id = :firmId';
				$whereParams[':firmId'] = $_POST['firm-id'];
			}
			if (@$_POST['ward-id']) {
				$whereSql .= ' AND w.id = :wardId';
				$whereParams[':wardId'] = $_POST['ward-id'];
			}
		}

		$whereSql .= ' AND ( e.deleted = 0 OR e.deleted is null ) AND ( ep.deleted = 0 OR ep.deleted is null) ';

		$data = Yii::app()->db->createCommand()
			->select('DISTINCT(o.id) AS operation_id, t.name, i.short_name as site_name, s.date, s.start_time, s.end_time,
					s.id AS session_id, TIMEDIFF(s.end_time, s.start_time) AS session_duration, s.comments AS session_comments,
					s.consultant as session_consultant, s.anaesthetist as session_anaesthetist, s.paediatric as session_paediatric,
					s.general_anaesthetic as session_general_anaesthetic, f.name AS firm_name, spec.name AS subspecialty_name, o.eye_id,
					an.name as anaesthetic_type, o.comments, b.admission_time, o.consultant_required, o.overnight_stay, e.id AS event_id,
					ep.id AS episode_id, p.id AS patient_id, o.total_duration AS duration, c.first_name, c.last_name, p.dob, p.gender,
					p.hos_num, w.name AS ward, b.display_order, b.confirmed, pr.name as priority, s.available, mu.first_name AS mu_fn,
					mu.last_name AS mu_ln, cu.first_name as cu_fn, cu.last_name as cu_ln, s.last_modified_date,
					su.first_name as session_first_name, su.last_name as session_last_name')
			->from('ophtroperation_operation_session s')
			->join('ophtroperation_operation_theatre t', 't.id = s.theatre_id')
			->leftJoin('site i', 'i.id = t.site_id')
			->leftJoin('ophtroperation_operation_booking b', 'b.session_id = s.id')
			->leftJoin('et_ophtroperation_operation o', 'o.id = b.element_id')
			->leftJoin('anaesthetic_type an','o.anaesthetic_type_id = an.id')
			->leftJoin('ophtroperation_operation_priority pr','pr.id = o.priority_id')
			->leftJoin('event e', 'e.id = o.event_id')
			->leftJoin('episode ep', 'ep.id = e.episode_id')
			->leftJoin('patient p', 'p.id = ep.patient_id')
			->leftJoin('contact c', "c.parent_id = p.id and c.parent_class = 'Patient'")
			->leftJoin('firm f', 'f.id = s.firm_id')
			->leftJoin('service_subspecialty_assignment ssa', 'ssa.id = f.service_subspecialty_assignment_id')
			->leftJoin('subspecialty spec', 'spec.id = ssa.subspecialty_id')
			->leftJoin('user mu','b.last_modified_user_id = mu.id')
			->leftJoin('user cu','b.created_user_id = cu.id')
			->leftJoin('user su','s.last_modified_user_id = su.id')
			->leftJoin('ophtroperation_operation_ward w', 'w.id = b.ward_id')
			->where($whereSql, $whereParams)
			->order('t.name ASC, s.date ASC, s.start_time ASC, s.end_time ASC, b.display_order ASC')
			->queryAll();

		$diary = array();

		foreach ($data as $row) {
			if (!isset($diary[$row['site_name']][$row['name']][$row['date']][$row['session_id']])) {
				$sessionTime = explode(':', $row['session_duration']);

				$diary[$row['site_name']][$row['name']][$row['date']][$row['session_id']] = array(
					'id' => $row['session_id'],
					'timestamp' => strtotime($row['date']),
					'site_name' => $row['site_name'],
					'theatre_name' => $row['name'],
					'firm_name' => $row['firm_name'],
					'subspecialty_name' => $row['subspecialty_name'],
					'start_time' => $row['start_time'],
					'end_time' => $row['end_time'],
					'duration' => ($sessionTime[0] * 60) + $sessionTime[1],
					'comments' => $row['session_comments'],
					'available' => $row['available'],
					'consultant' => $row['session_consultant'],
					'anaesthetist' => $row['session_anaesthetist'],
					'paediatric' => $row['session_paediatric'],
					'general_anaesthetic' => $row['session_general_anaesthetic'],
					'last_modified_date' => preg_replace('/ .*$/','',$row['last_modified_date']),
					'last_modified_time' => preg_replace('/^.* /','',$row['last_modified_date']),
					'session_first_name' => $row['session_first_name'],
					'session_last_name' => $row['session_last_name'],
					'bookings' => array(),
				);
			}

			if ($row['operation_id']) {
				$diary[$row['site_name']][$row['name']][$row['date']][$row['session_id']]['bookings'][] = array(
					'patient' => strtoupper($row['last_name']).', '.$row['first_name'].' ('.Helper::getAge($row['dob']).')',
					'hos_num' => $row['hos_num'],
					'gender' => $row['gender'],
					'operation_id' => $row['operation_id'],
					'eye_id' => $row['eye_id'],
					'anaesthetic_type' => $row['anaesthetic_type'],
					'comments' => $row['comments'],
					'admission_time' => $row['admission_time'],
					'consultant_required' => $row['consultant_required'],
					'overnight_stay' => $row['overnight_stay'],
					'event_id' => $row['event_id'],
					'duration' => $row['duration'],
					'ward' => $row['ward'],
					'confirmed' => $row['confirmed'],
					'priority' => $row['priority'],
					'created_user' => $row['cu_fn'].' '.$row['cu_ln'],
					'last_modified_user' => $row['mu_fn'].' '.$row['mu_ln'],
				);
			}
		}

		foreach ($diary as $site_name => $theatres) {
			foreach ($theatres as $theatre_name => $dates) {
				foreach ($dates as $date => $sessions) {
					foreach ($sessions as $session_id => $session) {
						$totalBookings = 0;
						foreach ($session['bookings'] as $booking) {
							$totalBookings += $booking['duration'];
						}
						$diary[$site_name][$theatre_name][$date][$session_id]['available_time'] = $session['duration'] - $totalBookings;
					}
				}
			}
		}

		return $diary;
	}
	
	public function getNextSessionDate($firmId) {
		$date = Yii::app()->db->createCommand()
			->select('date')
			->from('ophtroperation_operation_session s')
			->where('s.firm_id = :fid AND date >= CURDATE()', array(':fid' => $firmId))
			->order('date ASC')
			->limit(1)
			->queryRow();

		if (empty($date)) {
			// No sessions, return today
			return date('Y-m-d');
		} else {
			return $date['date'];
		}
	}

	public function getBookingList() {
		$from = Helper::convertNHS2MySQL($_POST['date-start']);
		$to = Helper::convertNHS2MySQL($_POST['date-end']);

		$whereSql = 't.site_id = :siteId and sp.id = :subspecialtyId and eo.status in (1,3) and date >= :dateFrom and date <= :dateTo';
		$whereParams = array(':siteId' => $_POST['site-id'], ':subspecialtyId' => $_POST['subspecialty-id'], ':dateFrom' => $from, ':dateTo' => $to);
		$order = 'w.name ASC, p.hos_num ASC';

		if ($_POST['ward-id']) {
			$whereSql .= ' and w.id = :wardId';
			$whereParams[':wardId'] = $_POST['ward-id'];
			$order = 'p.hos_num ASC';
		}else{
			$order = 'w.code ASC, p.hos_num ASC';
		}

		if ($_POST['firm-id']) {
			$whereSql .= ' and f.id = :firmId';
			$whereParams[':firmId'] = $_POST['firm-id'];
		}

		$whereSql .= ' and (ep.deleted = 0 or ep.deleted is null) and (e.deleted = 0 or e.deleted is null)';

		return Yii::app()->db->createCommand()
			->select('p.hos_num, c.first_name, c.last_name, p.dob, p.gender, s.date, w.code as ward_code, w.name as ward_name, f.pas_code as consultant, sp.ref_spec as subspecialty')
			->from('ophtroperation_operation_booking b')
			->join('ophtroperation_operation_session s','b.session_id = s.id')
			->join('ophtroperation_operation_theatre t','s.theatre_id = t.id')
			->join('firm f','f.id = s.firm_id')
			->join('service_subspecialty_assignment ssa','ssa.id = f.service_subspecialty_assignment_id')
			->join('subspecialty sp','sp.id = ssa.subspecialty_id')
			->join('et_ophtroperation_operation eo','b.element_id = eo.id')
			->join('event e','eo.event_id = e.id')
			->join('episode ep','e.episode_id = ep.id')
			->join('patient p','ep.patient_id = p.id')
			->join('contact c',"c.parent_id = p.id and c.parent_class = 'Patient'")
			->join('ophtroperation_operation_ward w','b.ward_id = w.id')
			->where($whereSql, $whereParams)
			->order($order)
			->queryAll();
	}

	/**
		* Generates a firm list based on a subspecialty id provided via POST
		* echoes form option tags for display
		*/
	public function actionFilterFirms()
	{
		echo CHtml::tag('option', array('value'=>''), CHtml::encode('All firms'), true);

		if (!empty($_POST['subspecialty_id'])) {
			$firms = $this->getFilteredFirms($_POST['subspecialty_id']);

			foreach ($firms as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id), CHtml::encode($name), true);
			}
		}
	}

	/**
		* Generates a theatre list based on a site id provided via POST
		* echoes form option tags for display
		*/
	public function actionFilterTheatres()
	{
		echo CHtml::tag('option', array('value'=>''), CHtml::encode('All theatres'), true);

		if (!empty($_POST['site_id'])) {
			$theatres = $this->getFilteredTheatres($_POST['site_id']);

			foreach ($theatres as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id), CHtml::encode($name), true);
			}
		}
	}

	/**
		* Generates a theatre list based on a site id provided via POST
		* echoes form option tags for display
		*/
	public function actionFilterWards()
	{
		echo CHtml::tag('option', array('value'=>''), CHtml::encode('All wards'), true);

		if (!empty($_POST['site_id'])) {
			$wards = $this->getFilteredWards($_POST['site_id']);

			foreach ($wards as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id), CHtml::encode($name), true);
			}
		}
	}

	public function actionSaveSessions() {
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$display_order = 1;

			$errors = array();

			// validation of theatre times
			foreach ($_POST as $key => $value) {
				if (preg_match('/^operation_([0-9]+)$/',$key,$operation_id)) {
					// This is validated in the model and the front-end so doesn't need an if ()
					preg_match('/^([0-9]{1,2}).*?([0-9]{2})$/',$value,$admission_time);
					$booking_ts = mktime($admission_time[1],$admission_time[2],0,1,1,date('Y'));
					$booking = Booking::model()->findByAttributes(array('element_operation_id' => $operation_id[1]));
					//preg_match('/^([0-9]{2}):([0-9]{2})/',$booking->session->start_time,$session_start_time);
					preg_match('/^([0-9]{2}):([0-9]{2})/',$booking->session->end_time,$session_end_time);
					//$session_from = mktime($session_start_time[1],$session_start_time[2],0,1,1,date('Y'));
					$session_to = mktime($session_end_time[1],$session_end_time[2],59,1,1,date('Y'));

					if ($booking_ts > $session_to) {
						$errors[] = array(
							'operation_id' => $operation_id[1],
							'message' => "The requested admission time is outside the window for this session."
						);
					}
				}
			}

			if (!empty($errors)) {
				die(json_encode($errors));
			}

			foreach ($_POST as $key => $value) {
				if (preg_match('/^operation_([0-9]+)$/',$key,$m)) {
					$booking = Booking::model()->findByAttributes(array('element_operation_id' => $m[1]));

					if (!empty($booking)) {
						// This is validated in the model and the front-end so doesn't need an if ()
						preg_match('/^([0-9]{1,2}).*?([0-9]{2})$/',$value,$m2);
						$value = $m2[1].":".$m2[2];

						$booking->confirmed = (@$_POST['confirm_'.$m[1]] ? 1 : 0);
						$booking->admission_time = $value;
						$booking->display_order = $display_order++;
						if (!$booking->save()) {
							throw new SystemException('Unable to save booking: '.print_r($booking->getErrors(),true));
						}

						$booking->elementOperation->event->audit('booking','update (diary)',$booking->getAuditAttributes());
					}
				}

				if (preg_match('/^comments_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->comments = $value;

						foreach ($_POST as $key => $value) {
							if (preg_match('/^consultant_([0-9]+)$/',$key,$n) && $m[1] == $n[1]) {
								$session->consultant = ($value == 'true' ? 1 : 0);
							}
							if (preg_match('/^paediatric_([0-9]+)$/',$key,$n) && $m[1] == $n[1]) {
								$session->paediatric = ($value == 'true' ? 1 : 0);
							}
							if (preg_match('/^anaesthetic_([0-9]+)$/',$key,$n) && $m[1] == $n[1]) {
								$session->anaesthetist = ($value == 'true' ? 1 : 0);
							}
							if (preg_match('/^general_anaesthetic_([0-9]+)$/',$key,$n) && $m[1] == $n[1]) {
								$session->general_anaesthetic = ($value == 'true' ? 1 : 0);
							}
							if (preg_match('/^available_([0-9]+)$/',$key,$n) && $m[1] == $n[1]) {
								$session->status= ($value == 'true' ? 0 : 1);
							}
						}

						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}

						$session->audit('session','update (diary)');
					}
				}
			}

			die(json_encode(array()));
		}
	}

	/**
		* Helper method to fetch firms by subspecialty ID
		*
		* @param integer $subspecialtyId
		*
		* @return array
		*/
	protected function getFilteredFirms($subspecialtyId)
	{
		$data = Yii::app()->db->createCommand()
			->select('f.id, f.name')
			->from('firm f')
			->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
			->join('subspecialty s', 'ssa.service_id = s.id')
			->where('ssa.subspecialty_id=:id',
				array(':id'=>$subspecialtyId))
			->queryAll();

		$firms = array();
		foreach ($data as $values) {
			$firms[$values['id']] = $values['name'];
		}

		natcasesort($firms);

		return $firms;
	}

	/**
		* Helper method to fetch theatres by site ID
		*
		* @param integer $siteId
		*
		* @return array
		*/
	protected function getFilteredTheatres($siteId)
	{
		$data = Yii::app()->db->createCommand()
			->select('t.id, t.name')
			->from('ophtroperation_operation_theatre t')
			->where('t.site_id = :id',
				array(':id'=>$siteId))
			->queryAll();

		$theatres = array();
		foreach ($data as $values) {
			$theatres[$values['id']] = $values['name'];
		}

		return $theatres;
	}

	/**
		* Helper method to fetch theatres by site ID
		*
		* @param integer $siteId
		*
		* @return array
		*/
	protected function getFilteredWards($siteId)
	{
		$data = Yii::app()->db->createCommand()
			->select('w.id, w.name')
			->from('ward w')
			->where('w.site_id = :id',
				array(':id'=>$siteId))
			->order('w.name ASC')
			->queryAll();

		$wards = array();
		foreach ($data as $values) {
			$wards[$values['id']] = $values['name'];
		}

		return $wards;
	}

	public function actionRequiresConsultant() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->consultant_required) {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionIsChild() {
		if (isset($_POST['patients']) && is_array($_POST['patients'])) {
			foreach ($_POST['patients'] as $hos_num) {
				if ($patient = Patient::Model()->find('hos_num = ?',array($hos_num))) {
					if ($patient->isChild()) {
						die("1");
					}
				} else {
					throw new SystemException('Patient not found: '.$hos_num);
				}
			}
		}
		die("0");
	}

	public function actionRequiresAnaesthetist() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->anaesthetist_required) {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionRequiresGeneralAnaesthetic() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->anaesthetic_type->name == 'GA') {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionSetFilter() {
		$so = Yii::app()->session['theatre_searchoptions'];
		foreach ($_POST as $key => $value) {
			$so[$key] = $value;
		}
		Yii::app()->session['theatre_searchoptions'] = $so;
	}

	public function actionGetSessionTimestamps() {
		if (isset($_POST['session_id'])) {
			if ($session = Session::model()->findByPk($_POST['session_id'])) {
				$ex = explode(' ',$session->last_modified_date);
				$last_modified_date = $ex[0];
				$last_modified_time = $ex[1];
				$user = User::model()->findByPk($session->last_modified_user_id);
				echo "Modified on ".Helper::convertMySQL2NHS($last_modified_date)." at ".$last_modified_time." by ".$user->first_name." ".$user->last_name;
			}
		}
	}
}
