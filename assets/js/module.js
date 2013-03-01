
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	handleButton($('#et_schedulelater'),function() {
		$('#schedule_now').val(0);
	});

	handleButton($('#et_schedulenow'),function() {
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

	handleButton($('#et_canceldelete'),function(e) {
		if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/delete/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
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

	handleButton($('#cancel'),function(e) {
		e.preventDefault();

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
			}
		});
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

	handleButton($('button#cancel_scheduling'),function(e) {
		document.location.href = baseUrl + '/patient/episodes/' + OE_patient_id;
		e.preventDefault();
	});

	handleButton($('#bookingForm button#confirm_slot'),function() {
		$('#bookingForm').submit();
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

	handleButton($('#btn_print-letter'),function() {
		var m = window.location.href.match(/\/view\/([0-9]+)$/);
		printPDF(baseUrl+'/OphTrOperationbooking/waitingList/printLetters',{'event_id': m[1]});
		enableButtons();
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
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/'+method,
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
