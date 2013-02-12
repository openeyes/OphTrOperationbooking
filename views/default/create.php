<?php $this->header() ?>

<h3 class="withEventIcon"><?php echo $this->event_type->name ?></h3>

<div>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
	));
	
	// Event actions
	$this->event_actions[] = EventAction::button('Save and Schedule later', 'scheduleLater', array('id' => 'et_save', 'colour' => 'green'));
	$this->event_actions[] = EventAction::button('Save and Schedule now', 'scheduleNow', array('id' => 'et_save_and_schedule', 'colour' => 'green'));
	$this->renderPartial('//patient/event_actions');
	?>
	<input type="hidden" name="schedule_now" id="schedule_now" value="0" />
	<?php
	$this->displayErrors($errors);
	$this->renderDefaultElements($this->action->id, $form);
	$this->renderOptionalElements($this->action->id, $form);
	$this->displayErrors($errors);
	?>
	<div class="cleartall"></div>
	<?php $this->endWidget()?>
</div>

<?php $this->footer()?>
