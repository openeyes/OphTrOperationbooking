
/* Module-specific javascript can be placed here */

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
	});

	$('#transport_checkall').click(function() {
		$('input[name^="bookings"]').attr('checked',$('#transport_checkall').is(':checked'));
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
