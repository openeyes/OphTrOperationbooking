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

$(document).ready(function() {
	$(this).undelegate('a.edit-session','click').delegate('a.edit-session','click',function() {
		cancel_edit();

		theatre_edit_session_id = $(this).attr('rel');

		theatre_edit_session_data = {};

		if ($('div.purpleUser').length >0) {
			theatre_edit_session_data["purple_rinse"] = {
				"consultant": $('#consultant_'+theatre_edit_session_id).is(':checked'),
				"paediatric": $('#paediatric_'+theatre_edit_session_id).is(':checked'),
				"anaesthetist": $('#anaesthetist_'+theatre_edit_session_id).is(':checked'),
				"general_anaesthetic": $('#general_anaesthetic_'+theatre_edit_session_id).is(':checked'),
				"available": $('#available_'+theatre_edit_session_id).is(':checked')
			};
		}

		theatre_edit_session_data["row_order"] = [];
		theatre_edit_session_data["confirm"] = {};

		$('#tbody_'+theatre_edit_session_id).children('tr').map(function(){
			theatre_edit_session_data["row_order"].push($(this).attr('id'));
			var id = $(this).attr('id').match(/[0-9]+/);
			theatre_edit_session_data["confirm"][id] = $('#confirm_'+id).is(':checked');
		});

		$('#tbody_'+theatre_edit_session_id+' .diaryViewMode').hide();
		$('div.session_options.diaryViewMode').hide();
		$('div.comments_ro.diaryViewMode').hide();
		$('.diaryEditMode[data-id="'+theatre_edit_session_id+'"]').show();
		$('.action_options[data-id="'+theatre_edit_session_id+'"]').show();

		$("#tbody_"+theatre_edit_session_id).sortable({
			 helper: function(e, tr) {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index) {
					 $(this).width($originals.eq(index).width())
				 });
				 return $helper;
			 },
			 placeholder: 'theatre-list-sort-placeholder'
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');

		$('tbody[id="tbody_'+theatre_edit_session_id+'"] td.confirm input[name^="confirm_"]').attr('disabled',false);
		$('th.footer').attr('colspan','10');

		return false;
	});

	$(this).undelegate('a.view-session','click').delegate('a.view-session','click',function() {
		cancel_edit();
		return false;
	});

	$('input[id^="consultant_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('consultant',session_id);
		}
	});

	$('input[id^="paediatric_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('paediatric',session_id);
		}
	});

	$('input[id^="anaesthetist_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('anaesthetist',session_id);
		}
	});

	$('input[id^="general_anaesthetic_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('general_anaesthetic',session_id);
		}
	});

	$(this).undelegate('button[id^="btn_edit_session_save_"]','click').delegate('button[id^="btn_edit_session_save_"]','click',function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			var session_id = $(this).attr('id').match(/[0-9]+/);

			$('input[name^="admitTime_"]').map(function() {
				var m = $(this).val().match(/^([0-9]{1,2}).*?([0-9]{2})$/);
				if (m) {
					if (m[1].length == 1) {
						m[1] = '0'+m[1];
					}
					$(this).val(m[1]+':'+m[2]);
				}
			});

			$.ajax({
				type: "POST",
				data: $('#session_form'+session_id).serialize()+"&session_id="+session_id,
				dataType: 'json',
				url: baseUrl+'/OphTrOperationbooking/theatreDiary/saveSession',
				success: function(errors) {
					var first = false;
					for (var operation_id in errors) {
						$('#oprow_'+operation_id).attr('style','background-color: #f00;');

						if (!first) {
							$('input[name="admitTime_'+operation_id+'"]').select().focus();
							first = true;
						}
					}

					if (first) {
						alert("One or more admission times were entered incorrectly, please correct the entries highlighted in red.");
						enableButtons();
						return false;
					}

					$('tr[id^="oprow_"]').attr('style','');

					$('#session_form'+session_id+' span.admitTime_ro').map(function() {
						$(this).text($('input[name="admitTime_'+$(this).attr('data-operation-id')+'"]').val());
					});

					$('div.comments_ro[data-id="'+session_id+'"]').text($('textarea[name="comments_'+session_id+'"]').val());

					function checkedOrOne(field) {
						if($(field).prop('type') == 'checkbox') {
							return $(field).is(':checked');
						} else if($(field).prop('type') == 'hidden') {
							return ($(field).val() == 1);
						}
					}
					
					checkedOrOne($('#available_'+session_id)) ? $('#session_unavailable_'+session_id).hide() : $('#session_unavailable_'+session_id).show();
					checkedOrOne($('#consultant_'+session_id)) ? $('#consultant_icon_'+session_id).show() : $('#consultant_icon_'+session_id).hide();
					checkedOrOne($('#anaesthetist_'+session_id)) ? $('#anaesthetist_icon_'+session_id).show() : $('#anaesthetist_icon_'+session_id).hide();
					$('#anaesthetist_icon_'+session_id).html(checkedOrOne($('#general_anaesthetic_'+session_id)) ? 'Anaesthetist (GA)' : 'Anaesthetist');
					checkedOrOne($('#paediatric_'+session_id)) ? $('#paediatric_icon_'+session_id).show() : $('#paediatric_icon_'+session_id).hide();

					cancel_edit(true);
					$('#infoBox_'+session_id).show();

					enableButtons();
				}
			});
		}

		return false;
	});

	$(this).undelegate('button[id^="btn_edit_session_cancel_"]','click').delegate('button[id^="btn_edit_session_cancel_"]','click',function() {
		cancel_edit();
		return false;
	});
});

function cancel_edit(dont_reset_checkboxes) {
	if (!dont_reset_checkboxes && theatre_edit_session_id != null) {
		for (var i in theatre_edit_session_data["purple_rinse"]) {
			$('#'+i+'_'+theatre_edit_session_id).attr('checked',(theatre_edit_session_data[i] ? 'checked' : false));
		}
	}

	if (theatre_edit_session_data) {
		var rows = '';

		for (var i in theatre_edit_session_data["row_order"]) {
			rows += '<tr id="'+theatre_edit_session_data["row_order"][i]+'">'+$('#'+theatre_edit_session_data["row_order"][i]).html()+'</tr>';
		}

		$('#tbody_'+theatre_edit_session_id).html(rows);

		for (var i in theatre_edit_session_data["row_order"]) {
			var id = theatre_edit_session_data["row_order"][i].match(/[0-9]+/);

			$('#confirm_'+id).attr('checked',(theatre_edit_session_data["confirm"][id] ? 'checked' : false));
		}
	}

	$('.diaryViewMode').show();
	$('.diaryEditMode').hide();
	$('.infoBox').hide();
	$('tbody[id="tbody_'+theatre_edit_session_id+'"] td.confirm input[name^="confirm_"]').attr('disabled','disabled');
	$('th.footer').attr('colspan','9');

	theatre_edit_session_id = null;
}

var theatre_edit_session_id = null;
var theatre_edit_session_data = null;

function checkRequired(type, session_id) {
	$.ajax({
		type: "POST",
		data: 'type='+type+'&session_id='+session_id,
		url: baseUrl+'/OphTrOperationbooking/theatreDiary/checkRequired',
		success: function(html) {
			if (html == "1") {
				$('#'+type+'_'+session_id).attr('checked',true);
				switch (type) {
					case 'consultant':
						alert("Sorry, you cannot remove the 'Consultant required' flag from this session because there are one or more patients booked into it who require a consultant.");
						break;
					case 'paediatric':
						alert("Sorry, you cannot remove the 'Paediatric' flag from this session because there are one or more patients booked into it who are paediatric.");
						break;
					case 'anaesthetist':
						alert("Sorry, you cannot remove the 'Anaesthetist required' flag from this session because there are one or more patients booked into it who require an anaesthetist.");
						break;
					case 'general_anaesthetic':
						alert("Sorry, you cannot remove the 'General anaesthetic available' flag from this session because there are one or more patients booked into it who require a general anaesthetic.");
						break;
				}

				return false;
			}
		}
	});
}
