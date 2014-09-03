<?php /**
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
<fieldset class="element-fields">
	<?php echo $form->radioButtons($element, 'eye_id', CHtml::listData(Eye::model()->findAll(array('order'=>'display_order asc')),'id','name'))?>
	<?php $form->widget('application.widgets.ProcedureSelection',array(
		'element' => $element,
		'durations' => true,
	))?>
	<?php echo $form->radioBoolean($element, 'consultant_required')?>
	<?php echo $form->radioButtons($element, 'anaesthetic_type_id', 'AnaestheticType')?>
	<?php echo $form->radioBoolean($element, 'overnight_stay')?>
	<?php echo $form->dropDownList($element, 'site_id', Site::model()->getListForCurrentInstitution(),array(),false,array('field'=>2))?>
	<?php echo $form->radioButtons($element, 'priority_id', 'OphTrOperationbooking_Operation_Priority')?>
	<?php
		if (Yii::app()->params['ophtroperationbooking_referral_link']) {
	?>
		<div class="row field-row">
	<?php
			if ($element->canChangeReferral()) {
				?>

				<div class="large-2 column">
					<label for="Element_OphTrOperationbooking_Operation_referral_id"><?= $element->getAttributeLabel('referral_id');?></label>
				</div>
				<div class="large-4 column">
					<?php
					$html_options = array('options' => array(), 'empty' => '- No valid referral available -', 'nowrapper' => true);
					$choices = $this->getReferralChoices();
					foreach ($choices as $choice) {
						if ($active_rtt = $choice->getActiveRTT()) {
							if (count($active_rtt) == 1) {
								$html_options['options'][(string) $choice->id] = array(
										'data-clock-start' => Helper::convertDate2NHS($active_rtt[0]->clock_start),
										'data-breach' => Helper::convertDate2NHS($active_rtt[0]->breach),
								);
							}
						}
					}
					echo $form->dropDownList($element, 'referral_id', CHtml::listData($this->getReferralChoices(),'id','description'),$html_options,false,array('field' => 2));
					?>
				</div>
				<div class="large-4 column end">
					<span id="rtt-info" class="rtt-info" style="display: none">Clock start - <span id="rtt-clock-start"></span> Breach - <span id="rtt-breach"></span></span>
				</div>
				<?php
			} else { ?>
					<div class="large-2 column"><label>Referral:</label></div>
					<div class="large-4 column end"><?php if ($element->referral) { echo $element->referral->getDescription(); } else { echo "No Referral Set"; } ?></div>
	<?php
			}
	?>
		</div>
	<?php
		}
	?>

	<?php echo $form->datePicker($element, 'decision_date', array('maxDate' => 'today'), array(), array_merge($form->layoutColumns, array('field' => 2)))?>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4), false, array(), array_merge($form->layoutColumns, array('field' => 4)))?>
	<?php echo $form->textArea($element, 'comments_rtt', array('rows' => 4), false, array(), array_merge($form->layoutColumns, array('field' => 4)))?>
	<div class="row field-row">
		<div class="large-2 column">
			<label for="<?= CHtml::modelName($element) . '[organising_admission_user_id]' ?>"><?= CHtml::encode($element->getAttributeLabel('organising_admission_user_id')) ?>:</label>
		</div>
		<div class="large-4 column end">
			<input type="hidden" id="<?= CHtml::modelName($element) . '_organising_admission_user_id' ?>" name="<?= CHtml::modelName($element) . '[organising_admission_user_id]' ?>">
			<?php
				$this->widget(
					'zii.widgets.jui.CJuiAutoComplete',
					array(
						'id' => 'organising_admission_user_autocomplete',
						'name' => 'organising_admission_user_autocomplete',
						'source' => $this->createUrl('/user/autoComplete'),
						'htmlOptions' => array('placeholder' => 'enter name'),
					)
				);
			?>
		</div>
	</div>
</fieldset>
