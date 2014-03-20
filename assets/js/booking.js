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
	handleButton($('#et_schedulelater'),function() {
		$('#schedule_now').val(0);
	});

	handleButton($('#et_save_and_schedule'),function() {
		$('#schedule_now').val(1);
	});

	handleButton($('#et_cancel'),function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_deleteevent'));

	handleButton($('#btn_reschedule-now'));

	handleButton($('#btn_cancel-operation'));

	handleButton($('#et_canceldelete'));

	$('select.populate_textarea').unbind('change').change(function() {
		if ($(this).val() != '') {
			var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
			var el = $('#'+cLass+'_'+$(this).attr('id'));
			var currentText = el.text();
			var newText = $(this).children('option:selected').text();

			if (currentText.length == 0) {
				el.text(ucfirst(newText));
			} else {
				el.text(currentText+', '+newText);
			}
		}
	});

	handleButton($('#cancel'),function(e) {
		e.preventDefault();

		$.ajax({
			type: 'POST',
			url: window.location.href,
			data: $('#cancelForm').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			dataType: 'json',
			success: function(data) {
				var n=0;
				var html = '';
				$.each(data, function(key, value) {
					html += '<ul><li>'+value+'</li></ul>';
					n += 1;
				});

				if (n == 0) {
					window.onbeforeunload ='';
					window.location.href = window.location.href.replace(/\/cancel\//,'/view/');
				} else {
					$('#cancelForm .alert-box').show();
					$('#cancelForm .alert-box').html(html);
					enableButtons();
				}


			}
		});
	});

	$('#calendar table td').click(function() {
		var day = $(this).text().match(/[0-9]+/);
		if (day == null) return false;
		window.location.href = URI(window.location.href).setSearch('day',day).removeSearch('session_id');
		return false;
	});

	handleButton($('button#cancel_scheduling'),function(e) {
		document.location.href = baseUrl + '/OphTrOperationbooking/default/view/' + OE_event_id;
		e.preventDefault();
	});

	handleButton($('#bookingForm button#confirm_slot'),function() {
		$('#bookingForm').submit();
	});

	$(this).undelegate('#firmSelect #firm_id','change').delegate('#firmSelect #firm_id','change',function() {
		var firm_id = $(this).val();
		var operation = $('input[id=operation]').val();
		window.onbeforeunload = null;
		window.location.href = URI(window.location.href).setSearch('firm_id',firm_id).removeSearch(['session_id', 'day']);
	});

	handleButton($('#btn_print-letter'),function() {
		var m = window.location.href.match(/\/view\/([0-9]+)$/);
		printIFrameUrl(baseUrl+'/OphTrOperationbooking/waitingList/printLetters',{'event_id': m[1]});
	});

	handleButton($('#btn_print-admissionletter'),function() {
		var m = window.location.href.match(/\/view\/([0-9]+)$/);
		printIFrameUrl(baseUrl+'/OphTrOperationbooking/default/admissionLetter/'+m[1]);
	});

	$('input[name="Element_OphTrOperationbooking_Diagnosis[eye_id]"]').change(function() {
		switch (parseInt($(this).val())) {
			case 2:
				$('#Element_OphTrOperationbooking_Operation_eye_id_2').click();
				break;
			case 1:
				$('#Element_OphTrOperationbooking_Operation_eye_id_1').click();
				break;
			case 3:
				if (!$('#Element_OphTrOperationbooking_Operation_eye_id_3').is(':checked')) {
					$('#Element_OphTrOperationbooking_Operation_eye_id_2').attr('checked',false);
					$('#Element_OphTrOperationbooking_Operation_eye_id_1').attr('checked',false);
					$('#Element_OphTrOperationbooking_Operation_eye_id_3').attr('checked',false);
				}
				break;
		}
	});
});
