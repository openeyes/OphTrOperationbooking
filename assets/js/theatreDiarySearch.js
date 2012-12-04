
$(document).ready(function() {
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

	$('#theatre-filter button[type="submit"]').click(function() {
		return getDiary();
	});

	$('#theatre-filter input[name=emergency_list]').change(function() {
		$('#site-id').attr("disabled", $(this).is(':checked'));
		$('#subspecialty-id').attr("disabled", $(this).is(':checked'));
		$('#theatre-id').attr("disabled", $(this).is(':checked'));
		$('#firm-id').attr("disabled", $(this).is(':checked'));
		$('#ward-id').attr("disabled", $(this).is(':checked'));
	});

	$('#date-filter_0').click(function() {
		today = new Date();

		clearBoundaries();

		$('#date-start').datepicker('setDate', format_date(today));
		$('#date-end').datepicker('setDate', format_date(today));

		setDiaryFilter({'date-filter':'today','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_1').click(function() {
		today = new Date();

		clearBoundaries();

		$('#date-start').datepicker('setDate', format_date(today));
		$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(today, 6)));

		setDiaryFilter({'date-filter':'week','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_2').click(function() {
		today = new Date();

		clearBoundaries();

		$('#date-start').val(format_date(today));
		$('#date-end').val(format_date(returnDateWithInterval(today, 29)));

		setDiaryFilter({'date-filter':'month','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_3').click(function() {

		setDiaryFilter({'date-filter':'custom','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});
	 
		return true;
	});

	$('#last_week').click(function() {
		sd = $('#date-start').val();

		clearBoundaries();

		if (sd == '') {
			today = new Date();
			$('#date-start').datepicker('setDate', format_date(returnDateWithInterval(today, -8)));
			$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(today, -1)));
		} else {
			$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(new Date(sd), -1)));
			$('#date-start').datepicker('setDate', format_date(returnDateWithInterval(new Date(sd), -7)));
		}

		setDiaryFilter({'date-filter':''});
		$('input[type="radio"]').attr('checked',true);
		$('#date-start').trigger('change');
		$('#date-end').trigger('change');
		return false;
	});

	$('#next_week').click(function() {
		ed = $('#date-end').val();

		clearBoundaries();

		if (ed == '') {
			today = new Date();

			$('#date-start').datepicker('setDate', format_date(today));
			$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(today, 7)));
		} else {
			today = new Date();

			if (ed == format_date(today)) {
				$('#date-start').datepicker('setDate', format_date(returnDateWithInterval(new Date(ed), 7)));
				$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(new Date(ed), 13)));
			} else {
				$('#date-start').datepicker('setDate', format_date(returnDateWithInterval(new Date(ed), 1)));
				$('#date-end').datepicker('setDate', format_date(returnDateWithInterval(new Date(ed), 7)));
			}
		}

		setDiaryFilter({'date-filter':''});
		$('input[type="radio"]').attr('checked',true);
		$('#date-start').trigger('change');
		$('#date-end').trigger('change');

		return false;
	});

	$('#date-start').bind('change',function() {
		$('#date-end').datepicker('option','minDate',$('#date-start').datepicker('getDate'));
	});

	$('#date-end').bind('change',function() {
		$('#date-start').datepicker('option','maxDate',$('#date-end').datepicker('getDate'));
	});

	$('select').change(function() {
		var hash = {};
		hash[$(this).attr('id')] = $(this).val();
		setDiaryFilter(hash);
	});

	$('#emergency_list').click(function() {
		if ($(this).is(':checked')) {
			setDiaryFilter({'emergency_list':1});
		} else {
			setDiaryFilter({'emergency_list':0});
		}
	});

	$('#date-start').change(function() {
		setDiaryFilter({'date-start':$(this).val()});
		$('#date-filter_3').attr('checked','checked');
	});

	$('#date-end').change(function() {
		setDiaryFilter({'date-end':$(this).val()});
		$('#date-filter_3').attr('checked','checked');
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

function getDiary() {
	var button = $('#theatre-filter button[type="submit"]');

	if (!button.hasClass('inactive')) {
		disableButtons();
		$('#theatreList').html('<h3 class="theatre firstTheatre">Please wait...</h3>');

		searchData = $('#theatre-filter').serialize();

		$.ajax({
			'url': baseUrl+'/OphTrOperation/theatreDiary/search',
			'type': 'POST',
			'data': searchData,
			'success': function(data) {
				$('#theatreList').html(data);
				enableButtons();
				return false;
			}
		});
	}

	return false;
}

function setDiaryFilter(values) {
	var data = '';
	var load_theatres_and_wards = false;

	for (var i in values) {
		if (data.length >0) {
			data += "&";
		}
		data += i + "=" + values[i];

		var field = i;
		var value = values[i];
	}

	$.ajax({
		'url': baseUrl+'/OphTrOperation/theatreDiary/setDiaryFilter',
		'type': 'POST',
		'data': data,
		'success': function(html) {
			if (field == 'site-id') {
				loadTheatresAndWards(value);
			} else if (field == 'subspecialty-id') {
				$.ajax({
					'url': baseUrl+'/OphTrOperation/theatreDiary/filterFirms',
					'type': 'POST',
					'data': 'subspecialty_id='+$('#subspecialty-id').val(),
					'success': function(data) {
						if ($('#subspecialty-id').val() != '') {
							$('#firm-id').attr('disabled', false);
							$('#firm-id').html(data);
						} else {
							$('#firm-id').attr('disabled', true);
							$('#firm-id').html(data);
						}
					}
				});
			}
		}
	});
}

function loadTheatresAndWards(siteId) {
	$.ajax({
		'type': 'POST',
		'data': {'site_id': siteId},
		'url': baseUrl+'/OphTrOperation/theatreDiary/filterTheatres',
		'success':function(data) {
			$('#theatre-id').html(data);
			$.ajax({
				'type': 'POST',
				'data': {'site_id': siteId},
				'url': baseUrl+'/OphTrOperation/theatreDiary/filterTheatres',
				'success':function(data) {
					$('#ward-id').html(data);
				}
			});
		}
	});
}

function clearBoundaries() {
	$('#date-start').datepicker('option','minDate', '').datepicker('option','maxDate', '');
	$('#date-end').datepicker('option','minDate', '').datepicker('option','maxDate', '');
}

function returnDateWithInterval(d, interval) {
	return new Date(d.getTime() + (86400000 * interval));
}
