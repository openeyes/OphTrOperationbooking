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
	<h3>Filters</h3>
	<form id="admin_sessions_filters">
		<div>
			<?php echo CHtml::dropDownList('firm_id',@$_GET['firm_id'],Firm::model()->getListWithSpecialtiesAndEmergency(),array('empty'=>'- Firm -'))?>
			&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('theatre_id',@$_GET['theatre_id'],CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Theatre -'))?>
			&nbsp;&nbsp;
			<span>From:</span>
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name'=>'date_from',
					'id'=>'date_from',
					// additional javascript options for the date picker plugin
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
					),
					'value'=>@$_GET['date_from'],
					'htmlOptions'=>array('style'=>'width: 110px;')
			))?>
			&nbsp;&nbsp;
			<span>To:</span>
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name'=>'date_to',
					'id'=>'date_to',
					// additional javascript options for the date picker plugin
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
					),
					'value'=>@$_GET['date_to'],
					'htmlOptions'=>array('style'=>'width: 110px;')
			))?>
			&nbsp;&nbsp;
			Seq: <?php echo CHtml::textField('sequence_id',@$_GET['sequence_id'],array('size'=>10))?>
		</div>
		<div>
			<?php echo CHtml::dropDownList('weekday',@$_GET['weekday'],array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'),array('empty'=>'- Weekday '))?>&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('consultant',@$_GET['consultant'],array(1=>'Yes',0=>'No'),array('empty'=>'- Consultant -'))?>&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('paediatric',@$_GET['paediatric'],array(1=>'Yes',0=>'No'),array('empty'=>'- Paediatric -'))?>&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('anaesthetist',@$_GET['anaesthetist'],array(1=>'Yes',0=>'No'),array('empty'=>'- Anaesthetist -'))?>&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('general_anaesthetic',@$_GET['general_anaesthetic'],array(1=>'Yes',0=>'No'),array('empty'=>'- General anaesthetic -'))?>&nbsp;&nbsp;
			<?php echo CHtml::dropDownList('available',@$_GET['available'],array(1=>'Yes',0=>'No'),array('empty'=>'- Available -'))?>&nbsp;&nbsp;
			<?php echo EventAction::button('Filter', 'filter', array('colour' => 'blue'))->toHtml()?>
			<?php echo EventAction::button('Reset', 'reset', array('colour' => 'blue'))->toHtml()?>
		</div>
	</form>
	<div class="reportInputs">
		<h3 class="georgia">Sessions<?php if (@$_GET['sequence_id']!='') {?> for sequence <?php echo $_GET['sequence_id']?><?php }?></h3>
		<div class="pagination">
			<?php echo $this->renderPartial('_pagination',array(
				'page' => $sessions['page'],
				'pages' => $sessions['pages'],
				'onePageOnly' => true,
			))?>
		</div>
		<div>
			<form id="admin_sessions">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" class="sessions" /></span>
						<span class="column_firm"><?php echo CHtml::link('Firm',$this->getUri(array('sortby'=>'firm')))?></span>
						<span class="column_theatre"><?php echo CHtml::link('Theatre',$this->getUri(array('sortby'=>'theatre')))?></span>
						<span class="column_date"><?php echo CHtml::link('Date',$this->getUri(array('sortby'=>'dates')))?></span>
						<span class="column_time"><?php echo CHtml::link('Time',$this->getUri(array('sortby'=>'time')))?></span>
						<span class="column_weekday"><?php echo CHtml::link('Weekday',$this->getUri(array('sortby'=>'weekday')))?></span>
						<span class="column_available">Available</span>
						<span class="column_attributes">Attributes</span>
						<input type="hidden" id="select_all" value="0" />
					</li>
					<?php if ($sessions['more_items']) {?>
						<li class="checkall_message" style="display: none;">
							<span class="column_checkall_message">
								All <?php echo count($sessions['data'])?> sessions on this page are selected. <a href="#" id="select_all_items">Select all <?php echo $sessions['count']?> sessions that match the current search criteria</a>
							</span>
						</li>
					<?php }?>
					<?php if (count($sessions['data']) <1) {?>
						<li class="no_results">
							<span class="column_no_results">
								No items matched your search criteria.
							</span>
						</li>
					<?php }?>
					<div class="sortable">
						<?php
						foreach ($sessions['data'] as $i => $session) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $session->id?>">
								<span class="column_checkbox"><input type="checkbox" name="session[]" value="<?php echo $session->id?>" class="sessions" /></span>
								<span class="column_firm"><?php echo $session->firm ? $session->firm->nameAndSubspecialtyCode: 'Emergency'?></span>
								<span class="column_theatre"><?php echo $session->theatre->name?></span>
								<span class="column_date"><?php echo $session->NHSDate('date')?></span>
								<span class="column_time"><?php echo $session->start_time?> - <?php echo $session->end_time?></span>
								<span class="column_weekday"><?php echo $session->weekdayText?></span>
								<span class="column_available"><?php echo $session->available ? 'Yes' : 'No'?></span>
								<span class="column_attributes">
									<span class="<?php echo $session->consultant ? 'set' : 'notset'?>">CON</span>
									<span class="<?php echo $session->paediatric ? 'set' : 'notset'?>">PAED</span>
									<span class="<?php echo $session->anaesthetist ? 'set' : 'notset'?>">ANA</span>
									<span class="<?php echo $session->general_anaesthetic ? 'set' : 'notset'?>">GA</span>
								</span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
	<div style="margin-bottom: 0.8em;">
		<a href="#" id="update_inline" style="display: none;">Update selected sessions</a>
	</div>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'inline_edit',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array('class'=>'sliding','style'=>'display: none;'),
	))?>
		<div>
			<div>
				<span class="label">Firm:</span>
				<?php echo CHtml::dropDownList('inline_firm_id','',Firm::model()->getListWithSpecialties(),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Theatre:</span>
				<?php echo CHtml::dropDownList('inline_theatre_id','',CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Date:</span>
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
						'name'=>'inline_date',
						'id'=>'inline_date',
						// additional javascript options for the date picker plugin
						'options'=>array(
							'showAnim'=>'fold',
							'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
						),
						'value'=>'',
						'htmlOptions'=>array('style'=>'width: 110px;')
				))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Start time:</span>
				<?php echo CHtml::textField('inline_start_time','',array('size'=>10))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">End time:</span>
				<?php echo CHtml::textField('inline_end_time','',array('size'=>10))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Consultant:</span>
				<?php echo CHtml::dropDownList('inline_consultant','',array(1=>'Yes',0=>'No'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Paediatric:</span>
				<?php echo CHtml::dropDownList('inline_paediatric','',array(1=>'Yes',0=>'No'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Anaesthetist:</span>
				<?php echo CHtml::dropDownList('inline_anaesthetist','',array(1=>'Yes',0=>'No'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">General anaesthetic:</span>
				<?php echo CHtml::dropDownList('inline_general_anaesthetic','',array(1=>'Yes',0=>'No'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Available:</span>
				<?php echo CHtml::dropDownList('inline_available','',array(1=>'Yes',0=>'No'),array('empty'=>'- Don\'t change -'))?>
				<span class="error"></span>
			</div>
			<div>
				<span class="label">Comments:</span>
				<?php echo CHtml::textArea('inline_comments','',array('rows'=>5,'cols'=>60))?>
			</div>
			<div>
				<?php echo EventAction::button('Update','update_inline',array('colour'=>'green'))->toHtml()?>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
				<span class="timeWarning" style="display: none;">Please be patient, it may take some time to process all the sessions ...</span>
			</div>
		</div>
	<?php $this->endWidget()?>
</div>
<div>
	<?php echo EventAction::button('Add', 'add_session', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete_session', array('colour' => 'blue'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<div id="confirm_delete_sessions" title="Confirm delete session" style="display: none;">
	<div>
		<div id="delete_sessions">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the sessions from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_sessions"><span class="button-span button-span-red">Remove session(s)</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_sessions"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_filter'),function(e) {
		e.preventDefault();

		var filterParams = $('#admin_sessions_filters').serialize();
		var urlParams = $(document).getUrlParams();

		for (var key in urlParams) {
			if (inArray(key,["page","sortby","order"])) {
				filterParams += "&"+key+"="+urlParams[key];
			}
		}

		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions?'+filterParams;
	});

	handleButton($('#et_reset'),function(e) {
		e.preventDefault();

		var filterFields = $('#admin_sessions_filters').serialize().match(/([a-z_]+)=/g);
		params = $(document).getUrlParams();
		for (var i in filterFields) {
			delete params[filterFields[i].replace(/=/,'')];
		}
		delete params['filter'];

		params = $.param(params);

		if (params != '?=') {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions?'+params+"&reset=1";
		} else {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions?reset=1';
		}
	});

	$('#inline_update_weeks').change(function() {
		if ($(this).val() == 1) {
			$('.inline_weeks').show();
		} else {
			$('.inline_weeks').hide();
		}
	});

	$('#update_inline').click(function(e) {
		e.preventDefault();
		$('#inline_edit').toggle('fast');
	});

	$('input[name="session[]"]').click(function() {
		if ($('input[name="session[]"]:checked').length >0) {
			$('#update_inline').show();
		} else {
			$('#update_inline').hide();
		}
	});

	$('#checkall').unbind('click').click(function() {
		$('input.'+$(this).attr('class')).attr('checked',$(this).is(':checked') ? 'checked' : false);
		if ($(this).is(':checked')) {
			$('#update_inline').show();
			$('li.checkall_message').show();
		} else {
			$('#update_inline').hide();
			$('#select_all').val(0);
			$('li.checkall_message').hide();
			$('span.column_checkall_message').html("All <?php echo $sessions['items_per_page']?> sessions on this page are selected. <a href=\"#\" id=\"select_all_items\">Select all <?php echo $sessions['count']?> sessions that match the current search criteria</a>");
		}
	});

	handleButton($('#et_update_inline'),function(e) {
		e.preventDefault();

		$('span.error').html('');

		if ($('#select_all').val() == 0) {
			var data = $('#admin_sessions').serialize()+"&"+$('#inline_edit').serialize();
		} else {
			var data = $('#admin_sessions_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
			$('span.timeWarning').show();
		}

		$.ajax({
			'type': 'POST',
			'dataType': 'json',
			'url': baseUrl+'/OphTrOperationbooking/admin/sessionInlineEdit',
			'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(errors) {
				var count = 0;
				for (var field in errors) {
					$('#inline_'+field).next('span.error').html(errors[field]);
					count += 1;
				}
				if (count >0) {
					alert("There were problems with the entries you made, please correct the errors and try again.");
					enableButtons();
				} else {
					window.location.reload();
				}
			}
		});
	});

	$('#select_all_items').live('click',function(e) {
		e.preventDefault();
		$('#select_all').val(1);
		$('span.column_checkall_message').html("All <?php echo $sessions['count']?> sessions that match the current search criteria are selected. <a href=\"#\" id=\"clear_selection\">Clear selection</a>");
	});

	$('#clear_selection').live('click',function(e) {
		e.preventDefault();
		$('#select_all').val(0);
		$('#checkall').removeAttr('checked');
		$('input[type="checkbox"][name="session[]"]').removeAttr('checked');
		$('li.checkall_message').hide();
		$('span.column_checkall_message').html("All <?php echo $sessions['items_per_page']?> sessions on this page are selected. <a href=\"#\" id=\"select_all_items\">Select all <?php echo $sessions['count']?> sessions that match the current search criteria</a>");
		$('#update_inline').hide();
	});

	handleButton($('#et_delete_session'),function(e) {
		e.preventDefault();

		if ($('#select_all').val() == 0 && $('input[type="checkbox"][name="session[]"]:checked').length <1) {
			alert("Please select the session(s) you wish to delete.");
			enableButtons();
			return;
		}

		if ($('#select_all').val() == 0) {
			var data = $('#admin_sessions').serialize()+"&"+$('#inline_edit').serialize();
		} else {
			var data = $('#admin_sessions_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSessions',
			'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					enableButtons();

					if ($('input[type="checkbox"][name="session[]"]:checked').length == 1) {
						$('#confirm_delete_sessions').attr('title','Confirm delete session');
						$('#delete_sessions').children('div').children('strong').html("WARNING: This will remove the session from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_sessions').children('span').text('Remove session');
					} else {
						$('#confirm_delete_sessions').attr('title','Confirm delete sessions');
						$('#delete_sessions').children('div').children('strong').html("WARNING: This will remove the sessions from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_sessions').children('span').text('Remove sessions');
					}

					$('#confirm_delete_sessions').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					alert("One or more of the selected sessions have active bookings and so cannot be deleted.");
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_sessions').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_sessions').dialog('close');
	});

	handleButton($('button.btn_remove_sessions'),function(e) {
		e.preventDefault();

		if ($('#select_all').val() == 0) {
			var data = $('#admin_sessions').serialize()+"&"+$('#inline_edit').serialize();
		} else {
			var data = $('#admin_sessions_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
		}

		// verify again as a precaution against race conditions
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSessions',
			'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					$.ajax({
						'type': 'POST',
						'url': baseUrl+'/OphTrOperationbooking/admin/deleteSessions',
						'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
						'success': function(resp) {
							if (resp == "1") {
								window.location.reload();
							} else {
								alert("There was an unexpected error deleting the sessions, please try again or contact support for assistance");
								enableButtons();
								$('#confirm_delete_sessions').dialog('close');
							}
						}
					});
				} else {
					alert("One or more of the selected sessions now have active bookings and so cannot be deleted.");
					enableButtons();
					$('#confirm_delete_sessions').dialog('close');
				}
			}
		});
	});
</script>
