
$(document).ready(function() {
	$(this).undelegate('a.edit-session','click').delegate('a.edit-session','click',function() {
		cancel_edit();

		theatre_edit_session_id = $(this).attr('rel');
		theatre_edit_session_data = {
			"consultant": $('#consultant_'+theatre_edit_session_id).is(':checked'),
			"paediatric": $('#paediatric_'+theatre_edit_session_id).is(':checked'),
			"anaesthetic": $('#anaesthetic_'+theatre_edit_session_id).is(':checked'),
			"general_anaesthetic": $('#general_anaesthetic_'+theatre_edit_session_id).is(':checked'),
			"available": $('#available_'+theatre_edit_session_id).is(':checked')
		};

		$('.diaryViewMode').hide();
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

	$('input[id^="anaesthetic_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('anaesthetic',session_id);
		}
	});

	$('input[id^="general_anaesthetic_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('general_anaesthetic',session_id);
		}
	});

	$(this).undelegate('button[id^="btn_save_"]','click').delegate('button[id^="btn_save_"]','click',function() {
	});
});

function cancel_edit() {
	if (theatre_edit_session_id != null) {
		for (var i in theatre_edit_session_data) {
			$('#'+i+'_'+theatre_edit_session_id).attr('checked',(theatre_edit_session_data[i] ? 'checked' : 'false'));
		}
	}

	$('.diaryViewMode').show();
	$('.diaryEditMode').hide();
	$('.infoBox').hide();
	$('th.footer').attr('colspan','9');
}

var theatre_edit_session_id = null;
var theatre_edit_session_data = null;

function checkRequired(type, session_id) {
	$.ajax({
		type: "POST",
		data: 'type='+type+'&session_id='+session_id,
		url: baseUrl+'/OphTrOperation/theatreDiary/checkRequired',
		success: function(html) {
			if (html == "1") {
				$('#'+type+'_'+session_id).attr('checked',true);
				switch (type) {
					case 'consultant':
						alert("Sorry, you cannot remove the 'Consultant required' flag from this session because there are one or more patients booked into it who require a consultant."); break;
					case 'paediatric':
						alert("Sorry, you cannot remove the 'Paediatric' flag from this session because there are one or more patients booked into it who are paediatric."); break;
					case 'anaesthetic':
						alert("Sorry, you cannot remove the 'Anaesthetist required' flag from this session because there are one or more patients booked into it who require an anaesthetist."); break;
					case 'general_anaesthetic':
						alert("Sorry, you cannot remove the 'General anaesthetic available' flag from this session because there are one or more patients booked into it who require a general anaesthetic."); break;
				}

				return false;
			}
		}
	});
}
