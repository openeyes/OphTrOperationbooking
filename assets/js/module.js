
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

	$('#calendar table td').click(function() {
		var day = $(this).text().match(/[0-9]+/);
		if (day == null) return false;

		if (window.location.href.match(/day=/)) {
			var href = window.location.href.replace(/day=[0-9]+/,'day='+day);
		} else if (window.location.href.match(/\?/)) {
			var href = window.location.href + '&day='+day;
		} else {
			var href = window.location.href + '?day='+day;
		}
		href = href.replace(/(&|\?)session_id=[0-9]+/,'');
		window.location.href = href;
		return false;
	});

	$('button#cancel_scheduling').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			document.location.href = baseUrl + '/patient/episodes/' + patient_id;
		}
		return false;
	});

	$('#bookingForm button#confirm_slot').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
	});

	$('#cancelForm button[type="submit"]').click(function(e) {
		var event_id = window.location.href.match(/[0-9]+/);

		if (!$(this).hasClass('inactive')) {
			$.ajax({
				type: 'POST',
				url: baseUrl+'/OphTrOperation/booking/update/'+event_id,
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
						window.location.href = baseUrl+'/OphTrOperation/default/view/'+event_id;
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

	$(this).undelegate('#firmSelect #firm_id','change').delegate('#firmSelect #firm_id','change',function() {
		var firm_id = $(this).val();
		var operation = $('input[id=operation]').val();
		if (window.location.href.match(/firm_id=/)) {
			var href = window.location.href.replace(/firm_id=([0-9]+|EMG)/,'firm_id='+firm_id);
		} else if (window.location.href.match(/\?/)) {
			var href = window.location.href + '&firm_id='+firm_id;
		} else {
			var href = window.location.href + '?firm_id='+firm_id;
		}
		href = href.replace(/(&|\?)day=[0-9]+/,'').replace(/(&|\?)session_id=[0-9]+/,'');
		window.location.href = href;
	});

	$('#btn_print-letter').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			printPDF(baseUrl+'/OphTrOperation/default/admissionLetter/'+window.location.href.match(/[0-9]+/),'');
			enableButtons();
		}
	});

	$("#btn_print_diary").click(function() {
		printElem('printDiary', {
			pageTitle:'openeyes printout',
			printBodyOptions:{styleToAdd:'width:auto !important; margin: 0.75em !important;',classNameToAdd:'openeyesPrintout'},overrideElementCSS:['css/style.css',{href:'css/style.css',media:'print'}]
		});
	});

	$('#btn_print_diary_list').click(function() {
		if ($('#site-id').val() == '' || $('#subspecialty-id').val() == '' || $('#date-start').val() == '' || $('#date-end').val() == '') {
			alert('To print the booking list you must select a site, a subspecialty and a date range.');
			scrollTo(0,0);
			return false;
		}

		printElem('printList',{
			pageTitle:'openeyes printout',
			printBodyOptions:{
				styleToAdd:'width:auto !important; margin: 0.75em !important;',
				classNameToAdd:'openeyesPrintout'
			},
			overrideElementCSS:['css/style.css',{href:'css/style.css',media:'print'}]
		});
	});
});

function printElem(method,options){
	$.ajax({
		'url': baseUrl+'/OphTrOperation/theatreDiary/'+method,
		'type': 'POST',
		'data': searchData,
		'success': function(data) {
			$('#printable').html(data);
			$('#printable').printElement(options);
			return false;
		}
	});
}

function theatreDiaryIconHovers() {
	var offsetY = 28;
	var offsetX = 10;
	var tipWidth = 0;

	$('.alerts img').hover(function(e){
		var titleText = $(this).attr('title');
		$(this).data('tipText',titleText).removeAttr('title');

		$('<p class="alertIconHelp"></p>').text(titleText).appendTo('body');
		$('<img />').attr({width:'17',height:'17',src:$(this).attr('src')}).prependTo('.alertIconHelp');
		tipWidth = $('.alertIconHelp').outerWidth();
		$('.alertIconHelp').css('top', (e.pageY - offsetY) + 'px').css('left', (e.pageX - (tipWidth + offsetX)) + 'px').fadeIn('fast');

	},function(e){
		$(this).attr('title',$(this).data('tipText'));
		$('.alertIconHelp').remove();

	}).mousemove(function(e) {
		$('.alertIconHelp')
			.css('top', (e.pageY - offsetY) + 'px')
			.css('left', (e.pageX - (tipWidth + offsetX)) + 'px');
	});
}
