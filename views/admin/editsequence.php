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
<div class="report curvybox white">
	<div class="admin">
		<h3 class="georgia"><?php echo $sequence->id ? 'Edit' : 'Add'?> sequence</h3>
		<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
		<div>
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'adminform',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus'=>'#username'
			))?>
			<?php echo $form->dropDownList($sequence,'firm_id',Firm::model()->getListWithSpecialties(),array('empty'=>'- Emergency -'))?>
			<?php echo $form->dropDownList($sequence,'theatre_id',CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
			<?php echo $form->datePicker($sequence,'start_date',array(),array('null'=>true))?>
			<?php echo $form->datePicker($sequence,'end_date',array(),array('null'=>true))?>
			<?php echo $form->textField($sequence,'start_time',array('size'=>10))?>
			<?php echo $form->textField($sequence,'end_time',array('size'=>10))?>
			<?php echo $form->dropDownList($sequence,'interval_id',CHtml::listData(OphTrOperationbooking_Operation_Sequence_Interval::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
			<?php echo $form->radioBoolean($sequence,'consultant')?>
			<?php echo $form->radioBoolean($sequence,'paediatric')?>
			<?php echo $form->radioBoolean($sequence,'anaesthetist')?>
			<?php echo $form->radioBoolean($sequence,'general_anaesthetic')?>
			<div id="OphTrOperationbooking_Operation_Sequence_week_selection" class="eventDetail">
				<div class="label">Week selection:</div>
				<div class="data">
					<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="0" />
					<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="0" />
					<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="0" />
					<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="0" />
					<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="0" />
					<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="1" />1st&nbsp;
					<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="1" />2nd&nbsp;
					<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="1" />3rd&nbsp;
					<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="1" />4th&nbsp;
					<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="1" />5th
				</div>
			</div>
			<?php $this->endWidget()?>
			<div class="view_sessions">
				<span>
					<?php echo CHtml::link('View sessions',Yii::app()->createUrl('/OphTrOperationbooking/admin/viewSessions?sequence_id='.$sequence->id))?>
				</span>
			</div>
		</div>
	</div>
</div>
<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Save', 'save', array('colour' => 'green'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('colour' => 'red'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences';
	});
	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});
</script>
