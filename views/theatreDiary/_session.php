<div class="infoBox" id="infoBox_<?php echo $id?>" style="display: none;">
	<strong>Session updated!</strong>
</div>

<div class="action_options" id="action_options_<?php echo $id?>" style="float: right;">
	<img id="loader_<?php echo $id?>" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;" />
	<div class="session_options">
		<span class="aBtn_inactive">View</span>
		<span class="aBtn edit-event">
			<a href="#" id="edit-sessions_<?php echo $id?>" class="edit-sessions">Edit</a>
		</span>
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
			<?php echo substr($session['start_time'], 0, 5)?>
			-
			<?php echo substr($session['end_time'], 0, 5)?>
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
			<div class="purpleUser" id="purple_rinse_<?php echo $id?>" style="display:none; width:207px;">
				<input type="checkbox" id="consultant_<?php echo $id?>" name="consultant_<?php echo $id?>" value="1"<?php if ($session['consultant']){?> checked="checked"<?php }?> /> Consultant present<br/>
				<input type="checkbox" id="paediatric_<?php echo $id?>" name="paediatric_<?php echo $id?>" value="1"<?php if ($session['paediatric']){?> checked="checked"<?php }?> /> Paediatric<br/>
				<input type="checkbox" id="anaesthetic_<?php echo $id?>" name="anaesthetic_<?php echo $id?>" value="1"<?php if ($session['anaesthetist']){?> checked="checked"<?php }?> /> Anaesthetist present<br/>
				<input type="checkbox" id="general_anaesthetic_<?php echo $id?>" name="general_anaesthetic_<?php echo $id?>" value="1"<?php if ($session['general_anaesthetic']){?> checked="checked"<?php }?> /> General anaesthetic available<br/>
				<input type="checkbox" id="available_<?php echo $id?>" name="available_<?php echo $id?>" value="1"<?php if ($session['available']){?> checked="checked"<?php }?> /> Session available<br/>
			</div>
		<?php }else{?>
			<input type="hidden" id="consultant_<?php echo $id?>" name="consultant_<?php echo $id?>" value="<?php if ($session['consultant']){ echo '1';} else { echo '0';}?>" />
			<input type="hidden" id="paediatric_<?php echo $id?>" name="paediatric_<?php echo $id?>" value="<?php if ($session['paediatric']){ echo '1';} else { echo '0';}?>" />
			<input type="hidden" id="anaesthetic_<?php echo $id?>" name="anaesthetic_<?php echo $id?>" value="<?php if ($session['anaesthetist']){ echo '1';} else { echo '0';}?>" />
			<input type="hidden" id="available_<?php echo $id?>" name="available_<?php echo $id?>" value="<?php if ($session['available']){ echo '1';} else { echo '0';}?>" />
		<?php }?>
		<div class="sessionComments" style="display:block; width:205px;">
			<form>
				<h4>Session Comments</h4>
				<textarea style="display: none;" rows="2" name="comments<?php echo $id ?>" id="comments<?php echo $id ?>"><?php echo $session['comments'] ?></textarea>
				<div id="comments_ro_<?php echo $id?>" title="Modified on <?php echo Helper::convertMySQL2NHS($session['last_modified_date'])?> at <?php echo $session['last_modified_time']?> by <?php echo $session['session_first_name']?> <?php echo $session['session_last_name']?>"><?php echo strip_tags($session['comments'])?></div>
			</form>
		</div>
	</div>
	<table class="theatre_list">
		<thead id="thead_<?php echo $id?>">
			<tr>
				<th>Admit time</th>
				<th class="th_sort" style="display: none;">Sort</th>
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
						<input style="display: none;" type="text" name="admitTime_<?php echo $booking['operation_id']?>" id="admitTime_<?php echo $id?>_<?php echo $booking['operation_id'] ?>" value="<?php echo substr($booking['admission_time'], 0, 5)?>" size="4">
						<span id="admitTime_ro_<?php echo $id?>_<?php echo $booking['operation_id']?>"><?php echo substr($booking['admission_time'], 0, 5)?></span>
					</td>
					<td class="td_sort" style="display: none;">
						<img src="<?php echo $assetPath?>/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28" />
					</td>
					<td class="hospital"><?php echo CHtml::link($booking['hos_num'], Yii::app()->createUrl('/OphTrOperation/default/view/'.$booking['event_id']));
					?></td>
					<td class="confirm"><input id="confirm_<?php echo $booking['operation_id']?>" type="checkbox" value="1" name="confirm_<?php echo $booking['operation_id']?>" disabled="disabled" <?php if ($booking['confirmed']) {?>checked="checked" <?php }?>/></td>
					<td class="patient leftAlign"><?php echo $booking['patient']?></td>
					<td class="operation leftAlign"><?php echo !empty($booking['procedures']) ? '['.$booking['eye'].'] '.$booking['procedures'] : 'No procedures'?></td>
					<td class=""><?php echo $booking['priority']?></td>
					<td class="anesthetic"><?php echo $booking['anaesthetic_type'] ?></td>
					<td class="ward"><?php echo $booking['ward']; ?></td>
					<td class="alerts">
						<?php if ($booking['gender'] == 'M') {?>
							<img src="<?php echo $assetPath?>img/diaryIcons/male.png" alt="male" title="male" width="17" height="17" />
						<?php } else {?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/female.png" alt="female" title="female" width="17" height="17" />
						<?php }?>
						<img src="<?php echo $assetPath?>/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed"<?php if (!$booking['confirmed']) {?> style="display: none;"<?php }?>>
						<?php if (!empty($booking['operationComments']) && preg_match('/\w/', $booking['operationComments'])) {?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/comment.png" alt="<?php echo htmlentities($booking['operationComments']) ?>" title="<?php echo htmlentities($booking['operationComments']) ?>" width="17" height="17" />
						<?php }?>
						<?php if (!empty($booking['overnightStay'])) {?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/overnight.png" alt="Overnight stay required" title="Overnight stay required" width="17" height="17" />
						<?php }?>
						<?php if (!empty($booking['consultantRequired'])) {?>
							<img src="<?php echo $assetPath?>/img/diaryIcons/consultant.png" alt="Consultant required" title="Consultant required" width="17" height="17" />
						<?php }?>
						<img src="<?php echo $assetPath?>/img/diaryIcons/booked_user.png" alt="Created by: <?php echo $booking['created_user']."\n"?>Last modified by: <?php echo $booking['last_modified_user']?>" title="Created by: <?php echo $booking['created_user']."\n"?>Last modified by: <?php echo $booking['last_modified_user']?>" width="17" height="17" /><?php
?>
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
</div>
