<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="box admin">
	<h2><?php echo $reason->id ? 'Edit' : 'Add'?> cancellation reason</h2>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'adminform',
			'enableAjaxValidation'=>false,
			'focus'=>'#username',
			'layoutColumns' => array(
				'label' => 2,
				'field' => 5
			)
		))?>
	<?php echo $form->errorSummary($reason); ?>
	<?php echo $form->dropDownList($reason,'list_id',CHtml::listData(OphTrOperationbooking_Operation_Cancellation_Reason_List::model()->findAll(array('order'=>'display_order')),'id','name'))?>
	<?php echo $form->textField($reason,'name')?>
	<?php echo $form->radioBoolean($reason,'active')?>
	<?php echo $form->formActions();?>
	<?php $this->endWidget()?>
	<?php echo $form->errorSummary($reason); ?>
</div>
<script type="text/javascript">
	$('#et_cancel').die('click').live('click',function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewCancellationReasons<?php if ($reason->list_id) {?>?list_id=<?php echo $reason->list_id;}?>';
	});
	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});
</script>
