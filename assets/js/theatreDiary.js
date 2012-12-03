
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
		if ($(this).is(':checked')) {
			$('#site-id').attr("disabled", true);
			$('#subspecialty-id').attr("disabled", true);
			$('#theatre-id').attr("disabled", true);
			$('#firm-id').attr("disabled", true);
			$('#ward-id').attr("disabled", true);
		} else {
			$('#site-id').attr("disabled", false);
			$('#subspecialty-id').attr("disabled", false);
			$('#theatre-id').attr("disabled", false);
			$('#firm-id').attr("disabled", false);
			$('#ward-id').attr("disabled", false);
		}
	});

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

var theatre_edit_session_id = null;
var theatre_edit_session_data = null;

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
