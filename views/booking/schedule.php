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

$this->header();
?>
<div id="schedule">
	<div class="patientReminder">
		<span class="patient"><?php echo $this->patient->getDisplayName()?> (<?php echo $this->patient->hos_num ?>)</span>
	</div>

	<h3><?php echo $operation->booking? 'Re-schedule' : 'Schedule'?> Operation</h3>

	<?php
	if ($event->episode->firm_id != $firm->id) {
		if ($firm->name == 'Emergency List') {
			$class = 'flash-error';
			$message = 'You are booking into the Emergency List.';
		} else {
			$class = 'flash-notice';
			$message = 'You are booking into the list for ' . $firm->name . '.';
		} ?>
		<div class="<?php echo $class; ?>"><?php echo $message; ?></div>
		<?php
	}
	if (empty($sessions)) { ?>
		<div class="flash-error">This firm has no scheduled sessions.</div>
		<?php
	}
	?>

	<?php if ($operation->booking) {?>
		<div class="eventDetail">
			<strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes
		</div>
		<div class="eventDetail">
			<div class="label"><strong>Current schedule:</strong></div>
			<?php $this->renderPartial('_session', array('operation' => $operation)); ?>
		</div>
	<?php }?>

	<div id="firmSelect" class="eventDetail clearfix">
		<div class="label"><span class="normal">Viewing the schedule for </span><br /><strong><?php echo $firm->name?></strong></div>
		<div class="data">
			<select id="firm_id">
				<option value="">Select a different firm</option>
				<option value="EMG">Emergency List</option>
				<?php foreach ($firmList as $id => $name) {?>
					<option value="<?php echo $id ?>"><?php echo $name ?></option>
				<?php }?>
			</select>
		</div>
	</div>

	<div id="operation">
		<h3>Select theatre slot</h3>

		<?php if (Yii::app()->user->hasFlash('info')) {?>
			<div class="flash-notice">
				<?php echo Yii::app()->user->getFlash('info'); ?>
			</div>
		<?php }?>

		<h4>Select a session date:</h4>
		<div id="calendar">
			<div id="session_dates">
				<div id="details">
					<?php echo $this->renderPartial('_calendar', array('operation'=>$operation, 'date'=>$date, 'firm' => $firm, 'selectedDate' => $selectedDate, 'sessions' => $sessions), false, true); ?>
				</div>
			</div>
		</div>

		<div id="theatres">
			<?php if ($theatres) {?>
				<?php echo $this->renderPartial('_theatre_times', array('operation'=>$operation, 'date'=>$selectedDate, 'theatres'=>$theatres, 'reschedule' => $operation->booking, 'firm' => $firm, 'selectedDate' => $selectedDate, 'selectedSession' => $session), false,true)?>
			<?php }?>
		</div>
		<div id="sessionDetails">
			<?php if ($session) {?>
				<?php echo $this->renderPartial('_list', array('operation'=>$operation, 'session'=>$session, 'bookings'=>$bookings, 'reschedule'=>$operation->booking, 'bookable'=>$bookable),false,true)?>
			<?php }?>
		</div>
	</div>
</div>
<?php $this->footer()?>
