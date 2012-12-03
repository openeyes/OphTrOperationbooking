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

//$baseUrl = Yii::app()->baseUrl;
//$cs = Yii::app()->getClientScript();
//$cs->registerCoreScript('jquery');
//$cs->registerCoreScript('jquery.ui');
//$cs->registerCoreScript('jquery.printElement.min');
//$cs->registerCSSFile('/css/jqueryui/theme/jquery-ui.css', 'all');

if (empty($diary)) {?>
	<p class="fullBox"><strong>No theatre schedules match your search criteria.</strong></p>
<?php }else{ $panels = array()?>
	<?php
	foreach ($diary as $site_name => $theatres) {
		foreach ($theatres as $theatre_name => $dates) {?>
			<h3 class="theatre"><strong><?php echo $theatre_name?> (<?php echo $site_name?>)</strong></h3>
			<?php foreach ($dates as $date => $sessions) {
				foreach ($sessions as $session_id => $session) {
					$this->renderPartial('_session',array('id' => $session_id, 'session'=>$session, 'bookings'=>$session['bookings'], 'assetPath'=>$assetPath));
				}
			}
		}
	}
}
?>
<script type="text/javascript">
	var table_states = {};
	var purple_states = {};

	$(document).ready(function() {
		load_table_states();
		<?php if (Yii::app()->user->checkAccess('purplerinse')) {?>
			load_purple_states();
		<?php }else{?>
			display_purple_states();
		<?php }?>
	});

	function load_table_states() {
		table_states = {};

		$('tbody').map(function() {
			if ($(this).attr('id') !== undefined) {
				var tbody_id = $(this).attr('id');

				table_states[tbody_id] = [];

				$(this).children('tr[id^="oprow_"]').map(function() {
					table_states[tbody_id].push($(this).attr('id'));
				});
			}
		});
	}

	function load_purple_states() {
		purple_states = {};

		$('tbody').map(function() {
			if ($(this).attr('id') !== undefined) {
				var tbody_id = $(this).attr('id').match(/[0-9]+/);

				purple_states[tbody_id] = {};

				purple_states[tbody_id]["consultant"] = $('#consultant_'+tbody_id).is(':checked');
				purple_states[tbody_id]["paediatric"] = $('#paediatric_'+tbody_id).is(':checked');
				purple_states[tbody_id]["anaesthetic"] = $('#anaesthetic_'+tbody_id).is(':checked');
				purple_states[tbody_id]["available"] = $('#available_'+tbody_id).is(':checked');
				purple_states[tbody_id]["general_anaesthetic"] = $('#general_anaesthetic_'+tbody_id).is(':checked');

				if ($('#consultant_'+tbody_id).is(':checked')) {
					$('#consultant_icon_'+tbody_id).show();
				} else {
					$('#consultant_icon_'+tbody_id).hide();
				}

				if ($('#paediatric_'+tbody_id).is(':checked')) {
					$('#paediatric_icon_'+tbody_id).show();
				} else {
					$('#paediatric_icon_'+tbody_id).hide();
				}

				if ($('#general_anaesthetic_'+tbody_id).is(':checked')) {
					$('#anaesthetist_icon_'+tbody_id).html('Anaesthetist (GA)');
				} else {
					$('#anaesthetist_icon_'+tbody_id).html('Anaesthetist');
				}

				if ($('#anaesthetic_'+tbody_id).is(':checked')) {
					$('#anaesthetist_icon_'+tbody_id).show();
				} else {
					$('#anaesthetist_icon_'+tbody_id).hide();
				}

				if ($('#available_'+tbody_id).is(':checked')) {
					$('#session_unavailable_'+tbody_id).hide();
				} else {
					$('#session_unavailable_'+tbody_id).show();
				}
			}
		});
	}

	function display_purple_states() {
		$('tbody').map(function() {
			if ($(this).attr('id') !== undefined) {
				var tbody_id = $(this).attr('id').match(/[0-9]+/);

				if ($('#consultant_'+tbody_id).val() == 1) {
					$('#consultant_icon_'+tbody_id).show();
				} else {
					$('#consultant_icon_'+tbody_id).hide();
				}

				if ($('#paediatric_'+tbody_id).val() == 1) {
					$('#paediatric_icon_'+tbody_id).show();
				} else {
					$('#paediatric_icon_'+tbody_id).hide();
				}

				if ($('#general_anaesthetic_'+tbody_id).val() == 1) {
					$('#anaesthetist_icon_'+tbody_id).html('Anaesthetist (GA)');
				} else {
					$('#anaesthetist_icon_'+tbody_id).html('Anaesthetist');
				}

				if ($('#anaesthetic_'+tbody_id).val() == 1) {
					$('#anaesthetist_icon_'+tbody_id).show();
				} else {
					$('#anaesthetist_icon_'+tbody_id).hide();
				}

				if ($('#available_'+tbody_id).val() == 1) {
					$('#session_unavailable_'+tbody_id).hide();
				} else {
					$('#session_unavailable_'+tbody_id).show();
				}
			}
		});
	}

	function enable_sort(session_id) {
		$("#tbody_"+session_id).sortable({
			 helper: function(e, tr)
			 {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index)
				 {
					 // Set helper cell sizes to match the original sizes
					 $(this).width($originals.eq(index).width())
				 });
				 return $helper;
			 },
			 placeholder: 'theatre-list-sort-placeholder'
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');
	}

	function disable_sort() {
		$("#theatre_list tbody").sortable('disable');
	}

	var selected_tbody_id = null;

	$(this).undelegate('a.view-sessions','click').delegate('a.view-sessions','click',function() {
		cancel_edit();
		return false;
	});

	$(this).undelegate('button[id^="btn_cancel_"]','click').delegate('button[id^="btn_cancel_"]','click',function() {
		if (!$(this).hasClass('inactive')) {
			$('#loader2_'+$(this).attr('id').match(/[0-9]+/)).show();
			disableButtons();
			setTimeout('edit_session_cancel_button('+$(this).attr('id').match(/[0-9]+/)+');',300);
		}
		return false;
	});

	function edit_session_cancel_button(id) {
		cancel_edit();
		enableButtons();
		$('#loader2_'+id).hide();
	}

	function view_mode() {
		$('div[id^="comments_ro_"]').show();
		$('textarea[name^="comments"]').hide();
		$('span[id^="admitTime_ro_"]').show();
		$('input[id^="admitTime_"]').hide();
		disable_sort();
		$('div.action_options').map(function() {
			var html = $(this).children('div.session_options').html();
			if (m = html.match(/edit-sessions_([0-9]+)/)) {
				$(this).children('div.session_options').html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
			if (m = html.match(/view-sessions_([0-9]+)/)) {
				$(this).children('div.session_options').html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
		});
		$('div.action_options').show();
		$('td.td_sort').hide();
		$('th.th_sort').hide();

		// revert text changes
		$('span[id^="admitTime_ro_"]').map(function() {
			var m = $(this).attr('id').match(/^admitTime_ro_([0-9]+)_([0-9]+)$/);
			$('#admitTime_'+m[1]+'_'+m[2]).val($(this).html());
		});
		$('div[id^="comments_ro_"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			$('#comments'+id).val($(this).html());
		});

		$('div.purpleUser').hide();
		$('#btn_print').show();
		$('input[name^="confirm_"]').attr('disabled',true);
		$('input[name^="confirm_"]').each(function(){
			if($(this).attr('data-ischecked') == "true"){
				$(this).attr('checked', "checked");
			}else{
				$(this).removeAttr('checked');
			}
		});
	}

	$('input[id^="consultant_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			$.ajax({
				type: "POST",
				data: "operations[]=" + operations.join("&operations[]="),
				url: "<?php echo Yii::app()->createUrl('theatre/requiresconsultant')?>",
				success: function(html) {
					if (html == "1") {
						$('#consultant_'+id).attr('checked',true);
						alert("Sorry, you cannot remove the 'Consultant required' flag from this session because there are one or more patients booked into it who require a consultant.");
						return false;
					}
				}
			});
		}
	});

	$('input[id^="paediatric_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			patients = [];

			$('#tbody_'+id).children('tr').map(function() {
				$(this).children('td.hospital').map(function() {
					$(this).children('a').map(function() {
						patients.push($(this).html());
					});
				});
			});

			$.ajax({
				type: "POST",
				data: "patients[]=" + patients.join("&patients[]="),
				url: "<?php echo Yii::app()->createUrl('theatre/ischild')?>",
				success: function(html) {
					if (html == "1") {
						$('#paediatric_'+id).attr('checked',true);
						alert("Sorry, you cannot remove the 'Paediatric' flag from this session because there are one or more patients booked into it who are paediatric.");
						return false;
					}
				}
			});
		}
	});

	$('input[id^="anaesthetic_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			if (operations.length >0) {
				$.ajax({
					type: "POST",
					data: "operations[]=" + operations.join("&operations[]="),
					url: "<?php echo Yii::app()->createUrl('theatre/requiresanaesthetist')?>",
					success: function(html) {
						if (html == "1") {
							$('#anaesthetic_'+id).attr('checked',true);
							alert("Sorry, you cannot remove the 'Anaesthetist required' flag from this session because there are one or more patients booked into it who require an anaesthetist.");
							return false;
						} else {
							$('#general_anaesthetic_'+id).attr('checked',false);
						}
					}
				});
			} else {
				$('#general_anaesthetic_'+id).attr('checked',false);
			}
		}
	});

	$('input[id^="general_anaesthetic_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			if (operations.length >0) {
				$.ajax({
					type: "POST",
					data: "operations[]=" + operations.join("&operations[]="),
					url: "<?php echo Yii::app()->createUrl('theatre/requiresgeneralanaesthetic')?>",
					success: function(html) {
						if (html == "1") {
							$('#general_anaesthetic_'+id).attr('checked',true);
							alert("Sorry, you cannot remove the 'General anaesthetic available' flag from this session because there are one or more patients booked into it who require a general anaesthetic.");
							return false;
						}
					}
				});
			}
		} else {
			$('#anaesthetic_'+id).attr('checked',true);
		}
	});

	// create alertIconHelp, using title icon
	$(document).ready(function(){
		var offsetY = 28;
		var offsetX = 10;
		var tipWidth = 0;
		
		$('.alerts img').hover(function(e){
			// over
			var titleText = $(this).attr('title');
			$(this)
				.data('tipText',titleText)
				.removeAttr('title');
			
			$('<p class="alertIconHelp"></p>')
				.text(titleText)
				.appendTo('body');
			// add icon
			$('<img />').attr({width:'17',height:'17',src:$(this).attr('src')}).prependTo('.alertIconHelp');
			// width?
			tipWidth = $('.alertIconHelp').outerWidth();	
			// position and fade in
			$('.alertIconHelp')
				.css('top', (e.pageY - offsetY) + 'px')
				.css('left', (e.pageX - (tipWidth + offsetX)) + 'px')
				.fadeIn('fast');
			
			
		},function(e){
			// out, reset HTML
			$(this).attr('title',$(this).data('tipText'));
			$('.alertIconHelp').remove();
			
		}).mousemove( function(e) {
			// track position
			$('.alertIconHelp')
				.css('top', (e.pageY - offsetY) + 'px')
				.css('left', (e.pageX - (tipWidth + offsetX)) + 'px');
		});
	}); // ready

</script>
