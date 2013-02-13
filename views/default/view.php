<?php $this->header() ?>

<h3 class="withEventIcon"><?php  echo $this->event_type->name ?> (<?php echo Element_OphTrOperation_Operation::model()->find('event_id=?',array($this->event->id))->status->name?>)</h3>

<?php $this->renderPartial('//base/_messages'); ?>

<?php if (!$operation->has_gp) {?>
	<div class="alertBox">
		Patient has no GP practice address, please correct in PAS before printing GP letter.
	</div>
<?php } ?>
<?php if (!$operation->has_address) { ?>
	<div class="alertBox">
		Patient has no address, please correct in PAS before printing letter.
	</div>
<?php } ?>

<?php if ($operation->event->hasIssue()) {?>
	<div class="issueBox">
		<?php echo $operation->event->getIssueText()?>
	</div>
<?php }?>

<div>
	<?php
	$this->renderDefaultElements($this->action->id);
	$this->renderOptionalElements($this->action->id);
	?>
	<div class="cleartall"></div>
</div>

<?php
	// Event actions
	$this->renderPartial('//patient/event_actions');
?>

<?php  $this->footer() ?>
