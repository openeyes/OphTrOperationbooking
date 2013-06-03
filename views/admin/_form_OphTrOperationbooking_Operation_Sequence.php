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
		<h3 class="georgia">Create / update sequence</h3>
		<?php echo $this->renderPartial('_form_errors', array('errors' => $errors))?>
		<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id' => 'adminform',
				'enableAjaxValidation' => false,
				'htmlOptions' => array('class' => 'sliding'),
			))
		?>
		<div>
			<?php echo $form->dropDownList($model, 'firm_id', $model->getFirmOptions(), array('empty' => 'Emergency')) ?>
			<?php echo $form->dropDownList($model, 'theatre_id', $model->getTheatreOptions(), array('empty' => '-- select --')) ?>
			<?php echo $form->textField($model, 'start_date') ?>
			<?php echo $form->textField($model, 'end_date') ?>
			<?php echo $form->textField($model, 'start_time') ?>
			<?php echo $form->textField($model, 'end_time') ?>
			<?php echo $form->radioBoolean($model, 'consultant') ?>
			<?php echo $form->radioBoolean($model, 'paediatric') ?>
			<?php echo $form->radioBoolean($model, 'anaesthetist') ?>
			<?php echo $form->radioBoolean($model, 'general_anaesthetic') ?>
			<?php echo $form->dropDownList($model, 'weekday', $model->getWeekdayOptions(), array('empty' => '-- select --')) ?>
			<?php echo $form->dropDownList($model, 'interval_id', $model->getIntervalOptions(), array('empty' => '-- select --')) ?>
			</div>
		<div>
			<?php echo EventAction::button('Save', 'save', array('colour' => 'green'))->toHtml()?>
			<?php echo EventAction::button('Cancel', 'cancel', array('colour' => 'red'))->toHtml()?>
			<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
		<?php $this->endWidget()?>
		</div>
</div>
