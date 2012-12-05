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

		$exclude_sites = array(3,5);
		$exclude_theatres = array();

		!empty($exclude_sites) and $where .= ' and si.id not in ('.implode(',',$exclude_sites).') ';
		!empty($exclude_theatres) and $where .= ' and s.theatre_id not in ('.implode(',',$exclude_theatres).') ';

		foreach (Yii::app()->db->createCommand()
			->select("eo.id as eoid, eo.priority_id, b.id as booking_id, p.id as pid, ev.id as evid, c.first_name, c.last_name, p.hos_num, eo.eye_id, f.pas_code as firm,
				eo.decision_date, su.ref_spec as subspecialty, s.date as session_date, s.start_time as session_time, eo.status_id, b.created_date,
				w.name as ward_name, s.theatre_id, s.id as session_id, b.cancellation_date, si.short_name as site_short_name, si.name as site_name, b.transport_arranged")
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
			->join("ophtroperation_operation_session s","s.id = b.session_id")
			->join("ophtroperation_operation_theatre t","t.id = s.theatre_id")
			->join("site si","si.id = t.site_id")
			->join("ward w","w.id = b.ward_id")
			->where("(ev.deleted = 0 or ev.deleted is null) and (e.deleted = 0 or e.deleted is null) and (b.transport_arranged = 0 or (b.transport_arranged = 1 and b.last_modified_date >= '$today')) $where")
			->order("session_date asc, session_time asc")
			->queryAll() as $i => $row) {

			if ($row['cancellation_date']) {
				$row['method'] = 'Cancelled';
			} else if ($row['status_id'] == 2) {
				$row['method'] = 'Booked';
			} else {
				$row['method'] = 'Rescheduled';
			}

			$ts = strtotime($row['session_date'].' '.$row['session_time']);
			$row['location'] = $row['site_short_name'] ? $row['site_short_name'] : $row['site_name'];

			if (($include_bookings && $row['method'] == 'Booked') || ($include_reschedules && $row['method'] == 'Rescheduled') || ($include_cancellations && $row['method'] == 'Cancelled')) {
				if (count($data_all) >= $offset && count($data) < $this->items_per_page) {
					while (isset($data[$ts])) $ts++;
					$data[$ts] = $row;
				}
				while (isset($data_all[$ts])) $ts++;
				$data_all[$ts] = $row;
			}
		}

		ksort($data);
		ksort($data_all);

		$this->total_items = count($data_all);
		$this->pages = ceil($this->total_items / $this->items_per_page);

		return array('bookings' => $data, 'bookings_all' => $data_all);
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
