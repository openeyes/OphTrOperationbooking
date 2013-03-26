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
?>
<div class="infoBox diaryViewMode" id="infoBox_<?php echo $id?>" style="display: none;">
	<strong>Session updated!</strong>
</div>

<?php $this->beginWidget('CActiveForm', array('id'=>'session_form'.$id, 'action'=>Yii::app()->createUrl('/OphTrOperationbooking/theatreDiary/saveSession'), 'enableAjaxValidation'=>false))?>
	<div class="action_options diaryViewMode" data-id="<?php echo $id?>" style="float: right;">
		<img id="loader_<?php echo $id?>" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;" />
		<div class="session_options diaryViewMode" data-id="<?php echo $id?>">
			<span class="aBtn_inactive">View</span>
			<?php if(BaseController::checkUserLevel(3)) { ?>
			<span class="aBtn edit-event">
				<a href="#" rel="<?php echo $id?>" class="edit-session">Edit</a>
			</span>
			<?php } ?>
		</div>
		<div class="session_options diaryEditMode" data-id="<?php echo $id?>" style="display: none;">
			<span class="aBtn view-event">
				<a href="#" rel="<?php echo $id?>" class="view-session">View</a>
			</span>
			<span class="aBtn_inactive edit-event">Edit</span>
		</div>
	</div>
	<h3 class="sessionDetails">
		<span class="date">
			<strong>
				<?php echo date('d M',$session['timestamp'])?>
			</strong>
			<?php echo date('Y',$session['timestamp'])?>
		</span>
		-
		<strong>
			<span class="day">
				<?php echo date('l',$session['timestamp'])?>
			</span>,
			<span class="time">
				<?php echo $session['start_time']?>
				-
				<?php echo $session['end_time']?>
			</span>
		</strong>
		for
		<?php echo !empty($session['firm_name']) ? $session['firm_name'] : 'Emergency List' ?>
		<?php echo !empty($session['subspecialty_name']) ? 'for (' . $session['subspecialty_name'] . ')' : '' ?>
		-
		<strong><?php echo $session['theatre_name'].' ('.$session['site_name'].')'?></strong>
	</h3>
	<div class="theatre-sessions whiteBox clearfix">
		<div style="float: right;">
			<?php if (Yii::app()->user->checkAccess('purplerinse')) {?>
				<div class="purpleUser diaryEditMode" data-id="<?php echo $id?>" style="display:none; width:207px;">
					<input type="hidden" name="consultant_<?php echo $id?>" value="0" />
					<input type="hidden" name="paediatric_<?php echo $id?>" value="0" />
					<input type="hidden" name="anaesthetist_<?php echo $id?>" value="0" />
					<input type="hidden" name="general_anaesthetic_<?php echo $id?>" value="0" />
					<input type="hidden" name="available_<?php echo $id?>" value="0" />
					<input type="checkbox" id="consultant_<?php echo $id?>" name="consultant_<?php echo $id?>" value="1"<?php if ($session['consultant']){?> checked="checked"<?php }?> /> Consultant present<br/>
					<input type="checkbox" id="paediatric_<?php echo $id?>" name="paediatric_<?php echo $id?>" value="1"<?php if ($session['paediatric']){?> checked="checked"<?php }?> /> Paediatric<br/>
					<input type="checkbox" id="anaesthetist_<?php echo $id?>" name="anaesthetist_<?php echo $id?>" value="1"<?php if ($session['anaesthetist']){?> checked="checked"<?php }?> /> Anaesthetist present<br/>
					<input type="checkbox" id="general_anaesthetic_<?php echo $id?>" name="general_anaesthetic_<?php echo $id?>" value="1"<?php if ($session['general_anaesthetic']){?> checked="checked"<?php }?> /> General anaesthetic available<br/>
					<input type="checkbox" id="available_<?php echo $id?>" name="available_<?php echo $id?>" value="1"<?php if ($session['available']){?> checked="checked"<?php }?> /> Session available<br/>
				</div>
			<?php }else{?>
				<input type="hidden" id="consultant_<?php echo $id?>" name="consultant_<?php echo $id?>" value="<?php echo $session['consultant']?>" />
				<input type="hidden" id="paediatric_<?php echo $id?>" name="paediatric_<?php echo $id?>" value="<?php echo $session['paediatric']?>" />
				<input type="hidden" id="anaesthetist_<?php echo $id?>" name="anaesthetist_<?php echo $id?>" value="<?php echo $session['anaesthetist']?>" />
				<input type="hidden" id="available_<?php echo $id?>" name="available_<?php echo $id?>" value="<?php echo $session['available']?>" />
			<?php }?>
			<div class="sessionComments" style="display:block; width:205px;">
				<form>
					<h4>Session Comments</h4>
					<textarea style="display: none;" rows="2" name="comments_<?php echo $id?>" class="comments diaryEditMode" data-id="<?php echo $id?>"><?php echo $session['comments']?></textarea>
					<div class="comments_ro diaryViewMode" data-id="<?php echo $id?>" title="Modified on <?php echo Helper::convertMySQL2NHS($session['last_modified_date'])?> at <?php echo $session['last_modified_time']?> by <?php echo $session['session_first_name']?> <?php echo $session['session_last_name']?>"><?php echo strip_tags($session['comments'])?></div>
				</form>
			</div>
		</div>
		<table class="theatre_list">
			<thead id="thead_<?php echo $id?>">
				<tr>
					<th>Admit time</th>
					<th class="th_sort diaryEditMode" data-id="<?php echo $id?>" style="display: none;">Sort</th>
					<th>Hospital #</th>
					<th>Confirmed</th>
					<th>Patient (Age)</th>
					<th>[Eye] Operation</th>
					<th>Priority</th>
					<th>Anesth</th>
					<th>Ward</th>
					<th>Info</th>
				</tr>
			</thead>
			<tbody id="tbody_<?php echo $id?>">
				<?php foreach ($bookings as $booking) {?>
					<tr id="oprow_<?php echo $booking['operation_id'] ?>">
						<td class="session">
							<input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_<?php echo $booking['operation_id']?>" data-id="<?php echo $id?>" data-operation-id="<?php echo $booking['operation_id']?>" value="<?php echo $booking['admission_time']?>" size="4">
							<span class="admitTime_ro diaryViewMode" data-id="<?php echo $id?>" data-operation-id="<?php echo $booking['operation_id']?>"><?php echo $booking['admission_time']?></span>
						</td>
						<td class="td_sort diaryEditMode" data-id="<?php echo $id?>" style="display: none;">
							<img src="<?php echo $assetPath?>/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28" />
						</td>
						<td class="hospital"><?php echo CHtml::link($booking['hos_num'], Yii::app()->createUrl('/OphTrOperationbooking/default/view/'.$booking['event_id']));
						?></td>
						<td class="confirm"><input type="hidden" name="confirm_<?php echo $booking['operation_id']?>" value="0" /><input id="confirm_<?php echo $booking['operation_id']?>" type="checkbox" value="1" name="confirm_<?php echo $booking['operation_id']?>" disabled="disabled" <?php if ($booking['confirmed']) {?>checked="checked" <?php }?>/></td>
						<td class="patient leftAlign"><?php echo $booking['patient_with_age']?></td>
						<td class="operation leftAlign"><?php echo !empty($booking['procedures']) ? '['.$booking['eye'].'] '.$booking['procedures'] : 'No procedures'?></td>
						<td class=""><?php echo $booking['priority']?></td>
						<td class="anesthetic"><?php echo $booking['anaesthetic_type'] ?></td>
						<td class="ward"><?php echo $booking['ward']; ?></td>
						<td class="alerts">
							<?php if ($booking['gender'] == 'M') {?>
								<img src="<?php echo $assetPath?>/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17" />
							<?php } else {?>
								<img src="<?php echo $assetPath?>/img/diaryIcons/female.png" alt="female" title="female" width="17" height="17" />
							<?php }?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed"<?php if (!$booking['confirmed']) {?> style="display: none;"<?php }?>>
							<?php if (!$booking['comments'] && preg_match('/\w/', $booking['comments'])) {?>
								<img src="<?php echo $assetPath?>/img/diaryIcons/comment.png" alt="<?php echo htmlentities($booking['comments']) ?>" title="<?php echo htmlentities($booking['comments']) ?>" width="17" height="17" />
							<?php }?>
							<?php if (!$booking['overnight_stay']) {?>
								<img src="<?php echo $assetPath?>/img/diaryIcons/overnight.png" alt="Overnight stay required" title="Overnight stay required" width="17" height="17" />
							<?php }?>
							<?php if (!$booking['consultant_required']) {?>
								<img src="<?php echo $assetPath?>/img/diaryIcons/consultant.png" alt="Consultant required" title="Consultant required" width="17" height="17" />
							<?php }?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/booked_user.png" alt="Created by: <?php echo $booking['created_user']."\n"?>Last modified by: <?php echo $booking['last_modified_user']?>" title="Created by: <?php echo $booking['created_user']."\n"?>Last modified by: <?php echo $booking['last_modified_user']?>" width="17" height="17" />
						</td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot>
				<tr>
					<?php $status = ($session['available_time'] > 0); ?>
					<th colspan="9" class="footer <?php echo ($status) ? 'available' : 'full'; ?> clearfix">
						<div class="session_timeleft">
							<?php if ($status) {?>
								<?php echo $session['available_time'] ?> minutes unallocated
							<?php }else{?>
								<?php echo abs($session['available_time']) ?> minutes overbooked
							<?php }?>
							<span<?php if ($session['available']) {?> style="display: none;"<?php }?> class="session_unavailable" id="session_unavailable_<?php echo $id?>"> - session unavailable</span>
						</div>
						<div class="specialists">
							<div<?php if (!$session['consultant']) {?> style="display: none;"<?php }?> id="consultant_icon_<?php echo $id?>" class="consultant" title="Consultant Present">Consultant</div>
							<div<?php if (!$session['anaesthetist']) {?> style="display: none;"<?php }?> id="anaesthetist_icon_<?php echo $id?>" class="anaesthetist" title="Anaesthetist Present">Anaesthetist<?php if ($session['general_anaesthetic']) {?> (GA)<?php }?></div>
							<div<?php if (!$session['paediatric']) {?> style="display: none;"<?php }?> id="paediatric_icon_<?php echo $id?>" class="paediatric" title="Paediatric Session">Paediatric</div>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
		<div style="display: none;" data-id="<?php echo $id?>" class="classy_buttons diaryEditMode">
			<img id="loader2_<?php echo $id?>" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 2px; display: none" />
			<button type="submit" class="classy green mini" id="btn_edit_session_save_<?php echo $id?>"><span class="button-span button-span-green">Save changes to session</span></button>
			<button type="submit" class="classy red mini" id="btn_edit_session_cancel_<?php echo $id?>"><span class="button-span button-span-red">Cancel</span></button>
		</div>
	</div>
<?php $this->endWidget()?>
