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

	$('#theatre_display #search-options select').change(function() {
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

function getDiary() {
	var button = $('#theatre-filter button[type="submit"]');

	if (!button.hasClass('inactive')) {
		disableButtons();
		$('#theatreList').html('<h3 class="theatre firstTheatre">Please wait...</h3>');

		searchData = $('#theatre-filter').serialize();

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/search',
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
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/setDiaryFilter',
		'type': 'POST',
		'data': data,
		'success': function(html) {
			if (field == 'site-id') {
				loadTheatresAndWards(value);
			} else if (field == 'subspecialty-id') {
				$.ajax({
					'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterFirms',
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
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterTheatres',
		'success':function(data) {
			$('#theatre-id').html(data);
			$.ajax({
				'type': 'POST',
				'data': {'site_id': siteId},
				'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterTheatres',
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
