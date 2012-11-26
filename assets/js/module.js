
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	$('#et_save').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			$('#schedule_now').val(0);
			disableButtons();
			return true;
		}
		return false;
	});

	$('#et_save_and_schedule').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			$('#schedule_now').val(1);
			disableButtons();
			return true;
		}
		return false;
	});

	$('#et_cancel').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			if (m = window.location.href.match(/\/update\/[0-9]+/)) {
				window.location.href = window.location.href.replace('/update/','/view/');
			} else {
				window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
			}
		}
		return false;
	});

	$('#et_deleteevent').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
		return false;
	});

	$('#et_canceldelete').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
				window.location.href = window.location.href.replace('/delete/','/view/');
			} else {
				window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
			}
		} 
		return false;
	});

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

	$('#cancel').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			$.ajax({
				type: 'POST',
				url: window.location.href,
				data: $('#cancelForm').serialize(),
				dataType: 'json',
				success: function(data) {
					var n=0;
					var html = '';
					$.each(data, function(key, value) {
						html += '<ul><li>'+value+'</li></ul>';
						n += 1;
					});

					if (n == 0) {
						window.location.href = window.location.href.replace(/\/cancel\//,'/view/');
					} else {
						$('div.alertBox').show();
						$('div.alertBox').html(html);
					}

					enableButtons();
					return false;
				}
			});
		}

		return false;
	});
});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		// handle event
	}
}
