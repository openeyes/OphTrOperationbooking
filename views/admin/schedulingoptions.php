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
<div class="report curvybox white">
	<div class="reportInputs">
		<h3 class="georgia">Scheduling options</h3>
		<div>
			<form id="admin_schedulingoptions">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" class="scheduleoptions" /></span>
						<span class="column_name">Name</span>
					</li>
					<div class="sortable">
						<?php
						$criteria = new CDbCriteria;
						$criteria->order = "display_order asc";
						foreach (OphTrOperationbooking_ScheduleOperation_Options::model()->findAll() as $i => $scheduleoption) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $scheduleoption->id?>">
								<span class="column_checkbox"><input type="checkbox" name="scheduleoption[]" value="<?php echo $scheduleoption->id?>" class="scheduleoptions" /></span>
								<span class="column_name"><?php echo $scheduleoption->name?></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add_scheduleoption', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete_scheduleoption', array('colour' => 'blue'))->toHtml()?>
</div>
<div id="confirm_delete_scheduleoptions" title="Confirm delete scheduleoption" style="display: none;">
	<div>
		<div id="delete_scheduleoptions">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the scheduleoptions from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_scheduleoptions"><span class="button-span button-span-red">Remove scheduleoption(s)</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_scheduleoptions"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_delete_scheduleoption'),function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="scheduleoption[]"]:checked').length <1) {
			alert("Please select the scheduling option(s) you wish to delete.");
			enableButtons();
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSchedulingOptions',
			'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					enableButtons();

					if ($('input[type="checkbox"][name="scheduleoption[]"]:checked').length == 1) {
						$('#confirm_delete_scheduleoptions').attr('title','Confirm delete scheduleoption');
						$('#delete_scheduleoptions').children('div').children('strong').html("WARNING: This will remove the scheduling option from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_scheduleoptions').children('span').text('Remove option');
					} else {
						$('#confirm_delete_scheduleoptions').attr('title','Confirm delete scheduleoptions');
						$('#delete_scheduleoptions').children('div').children('strong').html("WARNING: This will remove the scheduling options from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_scheduleoptions').children('span').text('Remove options');
					}

					$('#confirm_delete_scheduleoptions').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					alert("One or more of the selected scheduling options are in use by operations and so cannot be deleted.");
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_scheduleoptions').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_scheduleoptions').dialog('close');
	});

	handleButton($('button.btn_remove_scheduleoptions'),function(e) {
		e.preventDefault();

		// verify again as a precaution against race conditions
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSchedulingOptions',
			'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					$.ajax({
						'type': 'POST',
						'url': baseUrl+'/OphTrOperationbooking/admin/deleteSchedulingOptions',
						'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
						'success': function(resp) {
							if (resp == "1") {
								window.location.reload();
							} else {
								alert("There was an unexpected error deleting the scheduleoptions, please try again or contact support for assistance");
								enableButtons();
								$('#confirm_delete_scheduleoptions').dialog('close');
							}
						}
					});
				} else {
					alert("One or more of the selected scheduling options are now in use by operations and so cannot be deleted.");
					enableButtons();
					$('#confirm_delete_scheduleoptions').dialog('close');
				}
			}
		});
	});
</script>