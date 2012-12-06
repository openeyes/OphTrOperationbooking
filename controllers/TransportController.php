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

class TransportController extends BaseEventTypeController
{
	public $layout='//layouts/main';
	public $items_per_page = 100;
	public $page = 1;
	public $total_items = 0;
	public $pages = 1;
	public $js = array(
		'js/jquery.validate.min.js',
		'js/additional-validators.js',
	);

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
		if (ctype_digit(@$_GET['page'])) $this->page = $_GET['page'];
		$this->render('index',array('bookings' => $this->getTransportList()));
	}

	public function getTransportList() {
		if (!empty($_REQUEST)) {
			if (preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/',@$_REQUEST['date_from']) &&
				preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/',@$_REQUEST['date_to'])) {

				$date_from = Helper::convertNHS2MySQL($_REQUEST['date_from'])." 00:00:00";
				$date_to = Helper::convertNHS2MySQL($_REQUEST['date_to'])." 23:59:59";
			}
		} else {
			$_REQUEST['include_bookings'] = 1;
			$_REQUEST['include_reschedules'] = 1;
			$_REQUEST['include_cancellations'] = 1;
		}

		if (!@$_REQUEST['include_bookings'] && !@$_REQUEST['include_reschedules'] && !@$_REQUEST['include_cancellations']) {
			$_REQUEST['include_bookings'] = 1;
		}

		return $this->getTCIEvents(@$date_from, @$date_to, (boolean)@$_REQUEST['include_bookings'], (boolean)@$_REQUEST['include_reschedules'], (boolean)@$_REQUEST['include_cancellations']);
	}

	public function getTCIEvents($from, $to, $include_bookings, $include_reschedules, $include_cancellations) {
		$today = date('Y-m-d');

		if (!$include_bookings && !$include_reschedules && !$include_cancellations) {
			$this->total_items = $this->pages = 0;
			return array('bookings' => array(), 'bookings_all' => array());
		}

		$where = ($from && $to) ? " and s.date >= '$from' and s.date <= '$to' " : "";

		$offset = ($this->items_per_page * ($this->page-1));

		$data = array();
		$data_all = array();

		!empty(Yii::app()->params['transport_exclude_sites']) and $where .= ' and si.id not in ('.implode(',',Yii::app()->params['transport_exclude_sites']).') ';
		!empty(Yii::app()->params['transport_exclude_theatres']) and $where .= ' and s.theatre_id not in ('.implode(',',Yii::app()->params['transport_exclude_theatres']).') ';

		!$include_bookings and $where .= ' and (b.cancellation_date is not null or status_id != 2)';
		!$include_reschedules and $where .= ' and (b.cancellation_date is not null or status_id = 2)';
		!$include_cancellations and $where .= ' and (b.cancellation_date is null)';

		$this->total_items = Yii::app()->db->createCommand()
			->select("count(*)")
			->from("et_ophtroperation_operation eo")
			->join("event ev","eo.event_id = ev.id")
			->join("episode e","ev.episode_id = e.id")
			->join("firm f","e.firm_id = f.id")
			->join("service_subspecialty_assignment ssa","f.service_subspecialty_assignment_id = ssa.id")
			->join("subspecialty su","ssa.subspecialty_id = su.id")
			->join("patient p","e.patient_id = p.id")
			->join("contact c","c.parent_id = p.id and c.parent_class = 'Patient'")
			->join("(select element_id,max(id) as maxid from ophtroperation_operation_booking group by element_id) as btmp","btmp.element_id = eo.id")
			->join("ophtroperation_operation_booking b","b.id = btmp.maxid")
			->join("ophtroperation_operation_session s","s.id = b.session_id and s.date >= '$today'")
			->join("ophtroperation_operation_theatre t","t.id = s.theatre_id")
			->join("site si","si.id = t.site_id")
			->join("ward w","w.id = b.ward_id")
			->where("(ev.deleted = 0 or ev.deleted is null) and (e.deleted = 0 or e.deleted is null) and (b.transport_arranged = 0 or b.transport_arranged_date = '$today') $where")
			->queryScalar();

		$data = Yii::app()->db->createCommand()
			->select("eo.id as eoid, eo.priority_id, b.id as booking_id, p.id as pid, ev.id as evid, c.first_name, c.last_name, p.hos_num, eo.eye_id, f.pas_code as firm,
				eo.decision_date, su.ref_spec as subspecialty, s.date as session_date, s.start_time as session_time, eo.status_id, b.created_date, w.name as ward_name,
				s.theatre_id, s.id as session_id, b.cancellation_date, b.transport_arranged, unix_timestamp(str_to_date(concat(date,' ',start_time),'%Y-%m-%d %H:%i:%s')) as timestamp,
				case isnull(b.cancellation_date) when 0 then 'Cancelled' else ( case status_id = 2 when 1 then 'Booked' else 'Rescheduled' end ) end as method,
				case si.short_name != '' when 1 then si.short_name else si.name end as location, case eo.priority_id = 1 when 1 then 'Routine' else 'Urgent' end as priority,
				case transport_arranged = 0 when 1 then ( case s.date <= now() + interval 1 day when 1 then 'Red' else 'Green' end ) else 'Grey' end as colour")
			->from("et_ophtroperation_operation eo")
			->join("event ev","eo.event_id = ev.id")
			->join("episode e","ev.episode_id = e.id")
			->join("firm f","e.firm_id = f.id")
			->join("service_subspecialty_assignment ssa","f.service_subspecialty_assignment_id = ssa.id")
			->join("subspecialty su","ssa.subspecialty_id = su.id")
			->join("patient p","e.patient_id = p.id")
			->join("contact c","c.parent_id = p.id and c.parent_class = 'Patient'")
			->join("(select element_id,max(id) as maxid from ophtroperation_operation_booking group by element_id) as btmp","btmp.element_id = eo.id")
			->join("ophtroperation_operation_booking b","b.id = btmp.maxid")
			->join("ophtroperation_operation_session s","s.id = b.session_id and s.date >= '$today'")
			->join("ophtroperation_operation_theatre t","t.id = s.theatre_id")
			->join("site si","si.id = t.site_id")
			->join("ward w","w.id = b.ward_id")
			->where("(ev.deleted = 0 or ev.deleted is null) and (e.deleted = 0 or e.deleted is null) and (b.transport_arranged = 0 or b.transport_arranged_date = '$today') $where")
			->order("timestamp asc")
			->offset($offset)
			->limit($this->items_per_page)
			->queryAll();

		$this->pages = ceil($this->total_items / $this->items_per_page);

		return array('bookings' => $data);
	}

	public function actionDigest() {
		$times = Yii::app()->params['transport_csv_intervals'];

		foreach ($times as $i => $time) {
			if ($_GET['time'] == preg_replace('/:/','',$time)) {
				if ($i == 0) {
					$from = strtotime($_GET['date'].' '.$times[count($times)-1]) - 86400;
					$to = strtotime($_GET['date'].' '.$_GET['time']);
				} else {
					$from = strtotime($_GET['date'].' '.$last_time);
					$to = strtotime($_GET['date'].' '.$_GET['time']);
				}
				break;
			}

			$last_time = $time;
		}

		header("Content-Type: text/plain");
		header("Content-Description: File Transfer");
		header('Content-disposition: attachment; filename="'.$_GET['date'].'_'.$_GET['time'].'.csv"');
		header("Content-Transfer-Encoding: binary");

		$bookings = $this->getTCIEvents(date('Y-m-d H:i:s',$from), date('Y-m-d H:i:s',$to));

		echo "Hospital number,Patient,Session date,Session time,Site,Method,Firm,Subspecialty,Decision date,Priority\n";

		foreach ($bookings['bookings_all'] as $booking) {
			echo '"'.$booking['hos_num'].'","'.$booking['last_name'].', '.$booking['first_name'].'","'.$booking['session_date'].'","'.$booking['session_time'].'","'.$booking['location'].'","'.$booking['method'].'","'.$booking['firm'].'","'.$booking['subspecialty'].'","'.$booking['decision_date'].'","'.$booking['priority'].'"'."\n";
		}

		Yii::app()->end();
	}

	/**
	 * Print transport letters for bookings
	 */
	public function actionPrint($id) {
		$booking_ids = (isset($_REQUEST['booked'])) ? $_REQUEST['booked'] : null;
		if (!is_array($booking_ids)) {
			throw new CHttpException('400', 'Invalid booking list');
		}
		$bookings = OphTrOperation_Operation_Booking::model()->findAllByPk($booking_ids);

		// Print a letter for booking, separated by a page break
		$break = false;
		foreach($bookings as $booking) {
			if ($break) {
				$this->renderPartial("letters/break");
			} else {
				$break = true;
			}
			$patient = $booking->operation->event->episode->patient;
			$transport = array(
				'request_to' => 'FIXME: REQUEST TO',
				'request_from' => 'FIXME: REQUEST FROM',
				'escort' => '', // FIXME: No source yet
				'mobility' => '', // FIXME: No source yet
				'oxygen' => '', // FIXME: No source yet
				'contact_name' => 'FIXME: CONTACT NAME',
				'contact_number' => 'FIXME: CONTACT NUMBER',
				'comments' => '', // FIXME: No source yet
			);
			$this->renderPartial("transport/transport_form", array(
				'booking' => $booking,
				'patient' => $patient,
				'transport' => $transport,
			));
		}
	}

	public function actionConfirm() {
		if (is_array(@$_REQUEST['bookings'])) {
			foreach (@$_REQUEST['bookings'] as $booking) {
				if (!$booking->transport_arranged) {
					$booking->transport_arranged = 1;
					if (!$booking->save()) {
						throw new Exception('Unable to save booking: '.print_r($booking->getErrors(),true));
					}
				}
			}
		}

		echo '1';
	}

	public function actionDownloadcsv() {
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=transport.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "Hospital number,First name,Last name,TCI date,Admission time,Site,Ward,Method,Firm,Specialty,DTA,Priority\n";

		$data = $this->getBookings();

		foreach ($data['bookings_all'] as $row) {
			echo '"'.$row['hos_num'].'","'.trim($row['first_name']).'","'.trim($row['last_name']).'","'.$row['order_date'].'","'.$row['order_time'].'","'.$row['location'].'","'.$row['ward_name'].'","'.$row['method'].'","'.$row['firm'].'","'.$row['subspecialty'].'","'.$row['decision_date'].'","'.$row['priority'].'"'."\n";
		}
	}

	public function getUriAppend() {
		$return = array();
		foreach(array(	'date_from' => '', 'date_to' => '', 'include_bookings' => 0, 'include_reschedules' => 0, 'include_cancellations' => 0) as $token => $value) {
			if(isset($_REQUEST[$token])) {
				$return[] = $_REQUEST[$token];
			} else {
				$return[] = $value;
			}
		}
		return '/' . implode('/', $return);
	}
}
