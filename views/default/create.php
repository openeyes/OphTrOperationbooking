<?php
$this->breadcrumbs=array($this->module->id);
$this->header()?>
	<h3 class="withEventIcon" style="background:transparent url(<?php echo $this->assetPath?>/img/medium.png) center left no-repeat;"><?php echo $this->event_type->name ?></h3>
	<div>
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'clinical-create',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
		))?>
		<input type="hidden" name="schedule_now" id="schedule_now" value="0" />
		<?php
		$this->displayErrors($errors);
		$this->renderDefaultElements($this->action->id, $form);
		$this->renderOptionalElements($this->action->id, $form);
		$this->displayErrors($errors);
		?>
		<div class="cleartall"></div>
		<div class="form_button">
			<img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
			<button type="submit" class="classy green venti" id="et_save" name="scheduleLater"><span class="button-span button-span-green">Save and Schedule later</span></button>
			<button type="submit" class="classy green venti" id="et_save_and_schedule" name="scheduleNow"><span class="button-span button-span-green">Save and Schedule now</span></button>
			<button type="submit" class="classy red venti" id="et_cancel" name="cancelOperation"><span class="button-span button-span-red">Cancel Operation</span></button>
		</div>
		<?php $this->endWidget()?>
	</div>
<?php $this->footer()?>
