$(document).ready(function() {
	handleButton($('#et_add'),function(e) {
	});

	handleButton($('#drugs li'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/editWard/'+$(this).attr('data-attr-id');
	});
});
