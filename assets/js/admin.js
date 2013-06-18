$(document).ready(function() {
	$('#erod_rules li .column_subspecialty, #erod_rules li .column_firms').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/editERODRule/'+$(this).parent().attr('data-attr-id');
		}
	});

	$('#et_add_erod_rule').click(function() {
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addERODRule';
	});

	$('#et_delete_erod_rule').click(function() {
		if ($('input.erod_rules[type="checkbox"]:checked').length == 0) {
			alert("You haven't selected any rules to delete.");
		} else {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphTrOperationbooking/admin/deleteERODRules',
				'data': $('#erod_rules').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp == "1") {
						window.location.reload();
					} else {
						alert("Something went wrong trying to delete the rules. Please try again or contact support for assistance.");
					}
				}
			});
		}
	});
});
