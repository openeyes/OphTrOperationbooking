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

	$('#lcr_site_id').change(function() {
		var siteId = $(this).val();

		$('#letter_contact_rules li').children('a').removeAttr('style');

		$.ajax({
			'type': 'POST',
			'data': {'site_id': siteId, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN, 'empty': 1},
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterTheatres',
			'success':function(data) {
				$('#lcr_theatre_id').html(data);
			}
		});
	});

	$('#lcr_subspecialty_id').change(function() {
		var subspecialtyId = $(this).val();

		$('#rules li').children('a').removeAttr('style');

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterFirms',
			'type': 'POST',
			'data': 'subspecialty_id='+subspecialtyId+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN+"&empty=1",
			'success': function(data) {
				$('#lcr_firm_id').html(data);
			}
		});
	});

	$('#lcr_firm_id, #lcr_theatre_id, #lcr_is_child, #lcr_rule_type_id').change(function() {
		$('#rules li').children('a').removeAttr('style');

		if ($('#lcr_site_id').val() != '' && $('#lcr_subspecialty_id').val() != '' && $('#lcr_firm_id').val() != '' && $('#lcr_theatre_id').val() != '') {
			// only require these if they're in the dom
			if ($('#lcr_is_child').length >0 && $('#lcr_is_child').val() == '') return;
			if ($('#lcr_rule_type_id').length >0 && $('#lcr_rule_type_id').val() == '') return;

			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphTrOperationbooking/admin/test'+OE_rule_model+'s',
				'data': $('#rulestest').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'dataType': 'json',
				'success': function(resp) {
					for (var i in resp) {
						$('#rules li[id="'+resp[i]+'"]').children('a').attr('style','color: #f00');
					}
				}
			});
		}
	});

	$('#rules a.treenode').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/edit'+OE_rule_model+'/'+id;
	});

	$('#et_add_letter_contact_rule').click(function() {
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/add'+OE_rule_model;
	});

	$('#rules a.addTreeItemHere').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/add'+OE_rule_model+'?parent_rule_id='+$(this).attr('rel');
	});
});
