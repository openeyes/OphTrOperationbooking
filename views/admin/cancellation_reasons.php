<?php
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
?>
<div class="box admin">
	<h2>Cancellation reasons</h2>
	<div class="row field-row">
		<div class="large-1 column">
			List:
		</div>
		<div class="large-3 column end">
			<?php echo CHtml::dropDownList('list_id',$list->id,CHtml::listData(OphTrOperationbooking_Operation_Cancellation_Reason_List::model()->findAll(array('order' => 'display_order asc')),'id','name'))?>
		</div>
	</div>
	<form id="admin_reasons">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" class="wards" /></th>
					<th>List</th>
					<th>Name</th>
					<th>Active</th>
				</tr>
			</thead>
			<tbody class="sortable" data-sort-uri="/OphTrOperationbooking/admin/sortCancellationReasons">
				<?php
				$criteria = new CDbCriteria;
				$criteria->order = "`order` asc";
				$criteria->addCondition('list_id = :list_id');
				$criteria->params[':list_id'] = $list->id;

				foreach (OphTrOperationbooking_Operation_Cancellation_Reason::model()->findAll($criteria) as $i => $reason) {?>
					<tr class="clickable <?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $reason->id?>" data-uri="OphTrOperationbooking/admin/editCancellationReason/<?php echo $reason->id?>">
						<td><input type="checkbox" name="reasons[]" value="<?php echo $reason->id?>" class="cancellationReasons" /></td>
						<td><?php echo $reason->list->name?></td>
						<td><?php echo $reason->name?></td>
						<td><?php echo $reason->active ? 'Yes' : 'No'?>&nbsp;</td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo EventAction::link('Add', '#', null, array('class' => 'small button', 'id'=>'et_add_cancellation_reason'))->toHtml()?>
						<?php echo EventAction::link('Delete', '#', null, array('class' => 'small button','id'=>'et_delete_cancellation_reason'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="confirm_delete_wards" title="Confirm delete ward" style="display: none;">
	<div id="delete_wards">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the wards from the system.<br/>This action cannot be undone.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="medication_id" value="" />
			<button type="submit" class="warning btn_remove_wards">Remove ward(s)</button>
			<button type="submit" class="secondary btn_cancel_remove_wards">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.sortable').sortable({
			update: function (event, ui) {
				var ids = [];
				$('tbody.sortable').children('tr').map(function () {
					ids.push($(this).data('attr-id'));
				});
				$.ajax({
					'type': 'POST',
					'url': $('tbody.sortable').data('sort-uri'),
					'data': {order: ids, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
					'success': function (data) {
						new OpenEyes.UI.Dialog.Alert({
							content: 'Re-ordered'
						}).open();
					}
				});

			}
		}).disableSelection();

		$('#list_id').change(function(e) {
			window.location.href = baseUrl + '/OphTrOperationbooking/admin/viewCancellationReasons?list_id=' + $(this).val();
		});
	});

	$('#et_add_cancellation_reason').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl + '/OphTrOperationbooking/admin/addCancellationReason';
	});

	$('#et_delete_cancellation_reason').click(function(e) {
		e.preventDefault();

		if ($('input[name="reasons[]"]:checked').length == 0) {
			alert("Please select at least one cancellation reason to delete");
		} else {
			$.ajax({
				'type': 'POST',
				'data': $('#admin_reasons').serialize() + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
				'url': baseUrl+'/OphTrOperationbooking/admin/deleteCancellationReasons',
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						alert('There was an error deleting the cancellation reasons, please try again or contact support for assistance.');
					}
				}
			});
		}
	});

	$('#checkall').click(function(e) {
		$('input[name="reasons[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});
</script>
