<?php $this->header() ?>

<h3 class="withEventIcon"><?php echo $this->event_type->name ?></h3>

<div>
	<?php 
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
			'focus'=>'#procedure_id'
		));
		
		// Event actions
		$this->event_actions[] = EventAction::button('Save', 'save', array('id' => 'et_save', 'colour' => 'green'));
		$this->renderPartial('//patient/event_actions');
	?>
	<?php  $this->displayErrors($errors)?>
	<?php  $this->renderDefaultElements($this->action->id, $form); ?>
	<?php  $this->renderOptionalElements($this->action->id, $form); ?>
	<?php  $this->displayErrors($errors)?>
	<div class="cleartall"></div>
	<?php $this->endWidget(); ?>
</div>

<?php  $this->footer(); ?>