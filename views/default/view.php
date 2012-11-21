<?php
$this->breadcrumbs=array($this->module->id);
$this->header();
?>
<h3 class="withEventIcon" style="background:transparent url(<?php echo $this->assetPath?>/img/medium.png) center left no-repeat;"><?php  echo $this->event_type->name ?> (<?php echo Element_OphTrOperation_Operation::model()->find('event_id=?',array($this->event->id))->status->name?>)</h3>

<div>
	<?php
	$this->renderDefaultElements($this->action->id);
	$this->renderOptionalElements($this->action->id);
	?>
	<div class="cleartall"></div>
</div>

<?php  $this->footer();?>
