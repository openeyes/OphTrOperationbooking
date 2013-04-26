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
	handleButton($('button.btn_transport_viewall'),function(e) {
		$('#transport_date_from').val('');
		$('#transport_date_to').val('');
		$('#include_bookings').attr('checked','checked');
		$('#include_reschedules').attr('checked','checked');
		$('#include_cancellations').attr('checked','checked');
		transport_load_tcis();
		e.preventDefault();
	});

	handleButton($('button.btn_transport_filter'),function(e) {
		transport_load_tcis();
		e.preventDefault();
	});

	handleButton($('button.btn_transport_confirm'),function(e) {
		$.ajax({
			type: "POST",
			url: baseUrl+"/OphTrOperationbooking/transport/confirm",
			data: $('input[name^="bookings"]:checked').serialize(),
			success: function(html) {
				if (html == "1") {
					$('input[name^="bookings"]:checked').map(function() {
						$(this).parent().parent().attr('class','waitinglistGrey');
						$(this).attr('checked',false);
					});
				} else {
					alert("Something went wrong trying to confirm the transport item.\n\nPlease try again or contact OpenEyes support.");
				}
				enableButtons();
			}
		});
		e.preventDefault();
	});

	handleButton($('button.btn_transport_print'),function(e) {
		printUrl(window.location.href.replace(/\/index/,'/printList'));
		setTimeout('enableButtons();',3000);
		e.preventDefault();
	});

	handleButton($('button.btn_transport_download'),function() {
		$('#csvform').submit();
		enableButtons();
	});

	$('#transport_checkall').die('click').live('click',function() {
		$('input[name^="bookings"]').attr('checked',$('#transport_checkall').is(':checked') ? 'checked' : false);
	});

	$('a.pagination-link').die('click').live('click',function() {
		if (!$('button.btn_transport_filter').hasClass('inactive')) {
			disableButtons();
		}

		if ($(this).html().match(/next/)) {
			var page = parseInt($('span.transport_pagination_selected').html().match(/[0-9]+/)) + 1;
		} else if ($(this).html().match(/back/)) {
			var page = parseInt($('span.transport_pagination_selected').html().match(/[0-9]+/)) - 1;
		} else {
			var page = $(this).html().match(/[0-9]+/);
		}

		transport_load_tcis(page);

		return false;
	});

	$('#transport_date_from').bind('change',function() {
		$('#transport_date_to').datepicker('option','minDate',$('#transport_date_from').datepicker('getDate'));
	});

	$('#transport_date_to').bind('change',function() {
		$('#transport_date_from').datepicker('option','maxDate',$('#transport_date_to').datepicker('getDate'));
	});
});

var loadPage = null;

function transport_load_tcis(page) {
	if (page == null) page = 1;

	loadPage = page;

	if ($('span.transport_pagination_selected').length >0) {
		var currentPage = parseInt($('span.transport_pagination_selected').html().match(/[0-9]+/));

		if (parseInt(page) != currentPage) {
			$('span.transport_pagination_selected').replaceWith('<a class="pagination-link" rel='+currentPage+' href="'+baseUrl+'/OphTrOperationbooking/transport/index?page='+currentPage+'">'+currentPage+'</a>');
			$('a.pagination-link[rel="'+page+'"]').replaceWith('<span class="transport_pagination_selected">&nbsp;'+page+' </span>');

			if (parseInt(page) == 1) {
				$('span.transport_pagination_back').html('&laquo; back');
			} else if (currentPage == 1) {
				$('span.transport_pagination_back').html('<a class="pagination-link" rel="back" href="'+baseUrl+'/OphTrOperationbooking/transport/index?page='+currentPage+'">&laquo; back</a>');
			}

			if (parseInt(page) == transport_last_page()) {
				$('span.transport_pagination_next').html('next &raquo;');
			} else if (currentPage == transport_last_page()) {
				$('span.transport_pagination_next').html('<a class="pagination-link" rel="next" href="'+baseUrl+'/OphTrOperationbooking/transport/index?page='+currentPage+'">next &raquo;</a>');
			}
		}
	}

	$('#transportList tbody').html('<tr><td colspan="12"><img src="'+baseUrl+'/img/ajax-loader.gif" class="loader" /> loading data ...</td></tr>');

	var get = "page="+page;

	if (!$('#include_bookings').is(':checked')) get += "&include_bookings=0";
	if (!$('#include_reschedules').is(':checked')) get += "&include_reschedules=0";
	if (!$('#include_cancellations').is(':checked')) get += "&include_cancellations=0";

	if ($('#transport_date_from').val().length >0 && $('#transport_date_to').val().length >0) {
		get += "&date_from="+$('#transport_date_from').val()+"&date_to="+$('#transport_date_to').val();
	}

	$.ajax({
		type: "GET",
		url: baseUrl+"/OphTrOperationbooking/transport/tcis?"+get,
		success: function(html) {
			if (page == loadPage) {
				$('#transport_data').html(html);
				enableButtons();
			}
		}
	});
}

function transport_last_page() {
	var lastPage = 1;

	$('a.pagination-link').map(function() {
		if (parseInt($(this).attr('rel')) > lastPage) {
			lastPage = parseInt($(this).attr('rel'));
		}
	});

	var selectedPage = parseInt($('span.transport_pagination_selected').html().match(/[0-9]+/));

	if (selectedPage > lastPage) {
		return selectedPage;
	}

	return lastPage;
}
