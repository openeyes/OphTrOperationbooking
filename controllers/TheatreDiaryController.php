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

class TheatreDiaryController extends BaseEventTypeController
{
	public $layout='//layouts/main';

	public function accessRules() {
		return array(
			// Level 2 or below can't change anything
			array('deny',
				'actions' => array('savesessions', 'printdiary', 'printlist'),
				'expression' => '!BaseController::checkUserLevel(3)',
			),
			// Level 2 or above can do anything else
			array('allow',
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			array('deny'),
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

		$this->jsVars['NHSDateFormat'] = Helper::NHS_DATE_FORMAT;

		$this->render('index', array('wards'=>$wards, 'theatres'=>$theatres));
	}

	public function actionPrintDiary() {
		Audit::add('diary','print',serialize($_POST));

		Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl(
			Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets')
			).'/css/module.css'
		));

		$this->renderPartial('_print_diary', array('diary'=>$this->getDiary()), false, true);
	}

	public function actionPrintList() {
		Audit::add('diary','print list',serialize($_POST));

		Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl(
			Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets')
			).'/css/module.css'
		));

		$this->renderPartial('_print_list', array('bookings'=>$this->getBookingList()), false, true);
	}

	public function actionSearch()
	{
		Audit::add('diary','search',serialize($_POST));

		$this->renderPartial('_list', array('diary' => $this->getDiary(), 'assetPath'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphTrOperationbooking.assets'), false, -1, YII_DEBUG)), false, true);
	}

	public function getDiary() {
		$firmId = Yii::app()->session['selected_firm_id'];

		$_POST['date-start'] = Helper::convertNHS2MySQL(@$_POST['date-start']);
		$_POST['date-end'] = Helper::convertNHS2MySQL(@$_POST['date-end']);

		if (empty($_POST['date-start']) || empty($_POST['date-end'])) {
			$startDate = $endDate = $this->getNextSessionDate($firmId);
		} else {
			$startDate = $_POST['date-start'];
			$endDate = $_POST['date-end'];

			if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/',$startDate,$m)) {
				$m[1] = str_pad($m[1],2,0,STR_PAD_LEFT);
				$m[2] = str_pad($m[2],2,0,STR_PAD_LEFT);
				$startDate = "20{$m[3]}-{$m[2]}-{$m[1]}";
			}

			if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/',$endDate,$m)) {
				$m[1] = str_pad($m[1],2,0,STR_PAD_LEFT);
				$m[2] = str_pad($m[2],2,0,STR_PAD_LEFT);
				$endDate = "20{$m[3]}-{$m[2]}-{$m[1]}";
			}

			if (!strtotime($startDate) || !strtotime($endDate)) {
				throw new Exception('Invalid start and end dates.');
			}

			if (strtotime($endDate) < strtotime($startDate)) {
				list($startDate,$endDate) = array($endDate,$startDate);
			}
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
			->from('ophtroperationbooking_operation_session s')
			->join('ophtroperationbooking_operation_theatre t', 't.id = s.theatre_id')
			->leftJoin('site i', 'i.id = t.site_id')
			->leftJoin('ophtroperationbooking_operation_booking b', 'b.session_id = s.id and b.cancellation_date is null')
			->leftJoin('et_ophtroperationbooking_operation o', 'o.id = b.element_id')
			->leftJoin('anaesthetic_type an','o.anaesthetic_type_id = an.id')
			->leftJoin('ophtroperationbooking_operation_priority pr','pr.id = o.priority_id')
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
			->leftJoin('ophtroperationbooking_operation_ward w', 'w.id = b.ward_id')
			->where($whereSql, $whereParams)
			->order('i.short_name ASC, t.display_order ASC, t.code ASC, s.date ASC, s.start_time ASC, s.end_time ASC, b.display_order ASC')
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
					'patient' => strtoupper($row['last_name']).', '.$row['first_name'],
					'patient_with_age' => strtoupper($row['last_name']).', '.$row['first_name'].' ('.Helper::getAge($row['dob']).')',
					'age' => Helper::getAge($row['dob']),
					'hos_num' => $row['hos_num'],
					'gender' => $row['gender'],
					'operation_id' => $row['operation_id'],
					'eye' => Eye::model()->findByPk($row['eye_id'])->name,
					'anaesthetic_type' => $row['anaesthetic_type'],
					'comments' => $row['comments'],
					'admission_time' => substr($row['admission_time'],0,5),
					'consultant_required' => $row['consultant_required'],
					'overnight_stay' => $row['overnight_stay'],
					'event_id' => $row['event_id'],
					'duration' => $row['duration'],
					'ward' => $row['ward'],
					'confirmed' => $row['confirmed'],
					'priority' => $row['priority'],
					'created_user' => $row['cu_fn'].' '.$row['cu_ln'],
					'last_modified_user' => $row['mu_fn'].' '.$row['mu_ln'],
					'procedures' => Element_OphTrOperationbooking_Operation::model()->findByPk($row['operation_id'])->getProceduresCommaSeparated(),
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
			->from('ophtroperationbooking_operation_session s')
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

		$whereSql = 't.site_id = :siteId and sp.id = :subspecialtyId and eo.status_id in (2,4) and date >= :dateFrom and date <= :dateTo';
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
			->from('ophtroperationbooking_operation_booking b')
			->join('ophtroperationbooking_operation_session s','b.session_id = s.id')
			->join('ophtroperationbooking_operation_theatre t','s.theatre_id = t.id')
			->join('firm f','f.id = s.firm_id')
			->join('service_subspecialty_assignment ssa','ssa.id = f.service_subspecialty_assignment_id')
			->join('subspecialty sp','sp.id = ssa.subspecialty_id')
			->join('et_ophtroperationbooking_operation eo','b.element_id = eo.id')
			->join('event e','eo.event_id = e.id')
			->join('episode ep','e.episode_id = ep.id')
			->join('patient p','ep.patient_id = p.id')
			->join('contact c',"c.parent_id = p.id and c.parent_class = 'Patient'")
			->join('ophtroperationbooking_operation_ward w','b.ward_id = w.id')
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

	public function actionSaveSession() {
		if (!$session = OphTrOperationbooking_Operation_Session::model()->findByPk(@$_POST['session_id'])) {
			throw new Exception('Session not found: '.@$_POST['session_id']);
		}

		$errors = array();
		$bookings = array();

		foreach ($_POST as $key => $value) {
			if (preg_match('/^admitTime_([0-9]+)$/',$key,$m)) {
				if (!$operation = Element_OphTrOperationbooking_Operation::model()->findByPk($m[1])) {
					throw new Exception('Operation not found: '.$m[1]);
				}

				if (!$booking = $operation->booking) {
					throw new Exception('Operation has no active booking: '.$m[1]);
				}

				if ($booking->admission_time != $value || $booking->confirmed != @$_POST['confirm_'.$m[1]]) {
					$booking->admission_time = $value;
					$booking->confirmed = @$_POST['confirm_'.$m[1]];

					if (!$booking->validate()) {
						$formErrors = $booking->getErrors();
						$errors[(integer)$m[1]] = $formErrors['admission_time'][0];
					} else {
						$bookings[] = $booking;
					}
				}
			}
		}

		if (!empty($errors)) {
			echo json_encode($errors);
			return;
		}

		if (Yii::app()->user->checkAccess('purplerinse')) {
			$session->consultant = $_POST['consultant_'.$session->id];
			$session->paediatric = $_POST['paediatric_'.$session->id];
			$session->anaesthetist = $_POST['anaesthetist_'.$session->id];
			$session->general_anaesthetic = $_POST['general_anaesthetic_'.$session->id];
			$session->available = $_POST['available_'.$session->id];
		}

		$session->comments = $_POST['comments_'.$session->id];

		if (!$session->save()) {
			throw new Exception('Unable to save session: '.print_r($session->getErrors(),true));
		}

		foreach ($bookings as $i => $booking) {
			$booking->display_order = $i+1;

			if (!$booking->save()) {
				throw new Exception('Unable to save booking: '.print_r($session->getErrors(),true));
			}
		}

		echo json_encode(array());
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
			->from('ophtroperationbooking_operation_theatre t')
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
			->from('ophtroperationbooking_operation_ward w')
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

	public function actionSetDiaryFilter() {
		foreach ($_POST as $key => $value) {
			YiiSession::set('theatre_searchoptions',$key,$value);
		}
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

	public function actionCheckRequired() {
		if (!$session = OphTrOperationbooking_Operation_Session::model()->findByPk(@$_POST['session_id'])) {
			throw new Exception('Session not found: '.$_POST['session_id']);
		}

		switch (@$_POST['type']) {
			case 'consultant':
				if (Yii::app()->db->createCommand()
					->select("eo.consultant_required")
					->from("et_ophtroperationbooking_operation eo")
					->join("ophtroperationbooking_operation_booking b","b.element_id = eo.id")
					->join("ophtroperationbooking_operation_session s","b.session_id = s.id")
					->where("s.id = $session->id and eo.status_id in (2,4) and b.cancellation_date is null and eo.consultant_required = 1")
					->queryRow()) {
					echo "1";
				} else {
					echo "0";
				}
				return;
			case 'paediatric':
				$child_age = isset(Yii::app()->params['child_age_limit']) ? Yii::app()->params['child_age_limit'] : Patient::CHILD_AGE_LIMIT;
				$age_limit = date('Y')-$child_age.date('-m-d',time()+86400);

				if (Yii::app()->db->createCommand()
					->select("p.id")
					->from("patient p")
					->join("episode e","e.patient_id = p.id")
					->join("event ev","ev.episode_id = e.id")
					->join("et_ophtroperationbooking_operation eo","eo.event_id = ev.id")
					->join("ophtroperationbooking_operation_booking b","b.element_id = eo.id")
					->join("ophtroperationbooking_operation_session s","b.session_id = s.id")
					->where("s.id = $session->id and eo.status_id in (2,4) and b.cancellation_date is null and p.dob >= '$age_limit'")
					->queryRow()) { 
					echo "1";
				} else {
					echo "0";
				}
				return;
			case 'anaesthetist':
				if (Yii::app()->db->createCommand()
					->select("eo.anaesthetist_required")
					->from("et_ophtroperationbooking_operation eo")
					->join("ophtroperationbooking_operation_booking b","b.element_id = eo.id")
					->join("ophtroperationbooking_operation_session s","b.session_id = s.id")
					->where("s.id = $session->id and eo.status_id in (2,4) and b.cancellation_date is null and eo.anaesthetist_required = 1")
					->queryRow()) { 
					echo "1";
				} else {
					echo "0";
				}
				return;
			case 'general_anaesthetic':
				if (Yii::app()->db->createCommand()
					->select("eo.anaesthetist_required")
					->from("et_ophtroperationbooking_operation eo")
					->join("ophtroperationbooking_operation_booking b","b.element_id = eo.id")
					->join("ophtroperationbooking_operation_session s","b.session_id = s.id")
					->where("s.id = $session->id and eo.status_id in (2,4) and b.cancellation_date is null and eo.anaesthetic_type_id = 5")
					->queryRow()) {
					echo "1";
				} else {
					echo "0";
				}
				return;
		}

		throw new Exception('Unknown type: '.@$_POST['type']);
	}
}
