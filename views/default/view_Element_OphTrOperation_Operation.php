
<h4 class="elementTypeName">Procedure<?php if (count($element->procedures) != 1) echo 's'?></h4>

<div class="eventHighlight priority">
	<h4><?php echo $element->eye->adjective?> 
		<?php foreach ($element->procedures as $procedure) {
			echo "{$procedure->procedure->term}<br />";
		}
	?></h4>
</div>

<div class="cols2 clearfix">
	<div class="left">
		<h4>Anaesthetic</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->anaesthetic_type->name?></h4>
		</div>
	</div>

	<div class="right">
		<h4>Consultant required?</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->consultant_required ? 'Yes Consultant' : 'No Consultant'?></h4>
		</div>
	</div>

	<div class="left">
		<h4>Post Operative Stay Required</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->overnight_stay ? 'Yes Stay' : 'No Stay'?></h4>
		</div>
	</div>

	<div class="right">
		<h4>Decision Date</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->NHSDate('decision_date') ?></h4>
		</div>
	</div>

	<div class="left">
		<h4>Operation priority</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->priority->name?></h4>
		</div>
	</div>

	<?php if (!empty($element->comments)) { ?>
	<div class="right">
		<h4>Operation Comments</h4>
		<div class="eventHighlight comments">
			<h4><?php echo CHtml::encode($element->comments)?></h4>
		</div>
	</div>
	<?php } ?>
</div>

<div class="metaData">
	<span class="info">
		Operation created by <span class="user"><?php echo $element->event->user->fullname ?></span> on <?php echo $element->event->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($element->event->created_date)) ?>
	</span>
	<span class="info">
		Operation last modified by <span class="user"><?php echo $element->event->usermodified->fullname ?></span> on <?php echo $element->event->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($element->event->last_modified_date)) ?>
	</span>
</div>

<?php if ($element->booking) {?>
	<h3 class="subsection">Booking Details</h3>

	<div class="cols2">
		<div class="left">
			<h4>List</h4>
			<div class="eventHighlight">
				<?php $session = $element->booking->session ?>
				<h4 style="width: 460px;"><?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', '.$session->FirmName; ?></h4>
			</div>
		</div>

		<div>
			<h4>Theatre</h4>
			<div class="eventHighlight">
				<h4><?php echo $session->TheatreName ?></h4>
			</div>
		</div>

		<div>
			<h4>Admission Time</h4>
			<div class="eventHighlight">
				<h4><?php echo substr($element->booking->admission_time,0,5) ?></h4>
			</div>
		</div>
	</div>

	<div class="metaData">
	<span class="info">
	Booking created by <span class="user"><?php echo $element->booking->user->fullname ?></span> on <?php echo $element->booking->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($element->booking->created_date)) ?>
	</span>
	<span class="info">
	Booking last modified by <span class="user"><?php echo $element->booking->usermodified->fullname ?></span> on <?php echo $element->booking->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($element->booking->last_modified_date)) ?>
	</span>
	</div>
<?php } ?>

<?php if (count($element->cancelledBookings)) { ?>
	<h3 class="subsection">Cancelled Bookings</h3>
	<ul class="eventComments">
		<?php foreach($element->cancelledBookings as $booking) { ?>
		<li>
			Originally scheduled for <strong><?php echo $booking->NHSDate('session_date'); ?>,
			<?php echo date('H:i',strtotime($booking->session_start_time)); ?> -
			<?php echo date('H:i',strtotime($booking->session_end_time)); ?></strong>,
			in <strong><?php echo $booking->theatre->NameWithSite; ?></strong>.
			Cancelled on <?php echo $booking->NHSDate('cancellation_date'); ?>
			by <strong><?php echo $booking->user->FullName; ?></strong>
			due to <?php echo $booking->cancellationReasonWithComment; ?>
		</li>
		<?php }?>
	</ul>
<?php }?>

<?php if ($element->status->name == 'Cancelled' && $element->cancellation_date) {?>
	<h3 class="subsection">Cancellation details</h3>
		<div class="eventHighlight">
			<h4>Cancelled on <?php echo $element->NHSDate('cancellation_date') . ' by user ' . $element->cancellation_user->username . ' for reason: ' . $element->cancellation_reason->text?>
			</h4>
		</div>

	<?php if ($element->cancellation_comment) {?>
		<h4>Cancellation comments</h4>
		<div class="eventHighlight comments">
			<h4><?php echo str_replace("\n","<br/>",$element->cancellation_comment)?></h4>
		</div>
	<?php } ?>
<?php } ?>

<?php if ($element->erod) {?>
	<div>
		<h3 class="subsection">Earliest reasonable offer date</h3>
		<div class="eventHighlight">
			<h4><?php echo $element->erod->NHSDate('session_date').' '.$element->erod->timeSlot.', '.$element->erod->FirmName?></h4>
		</div>
	</div>
<?php }?>

<?php if ($element->status->name != 'Cancelled' && $this->event->editable) {?>
	<div style="margin-top:40px; text-align:center;">
		<?php
		if (empty($element->booking)) {
			if ($element->letterType) {
				if ($element->has_gp && $element->has_address) {?>
					<button type="submit" class="auto classy blue venti" value="submit" id="btn_print-invitation-letter"><span class="button-span button-span-blue">Print <?php echo $element->letterType ?> letter</span></button>
				<?php }else{?>
					<button type="submit" class="auto classy disabled venti" value="submit" disabled="disabled"><span class="button-span">Print <?php echo $element->letterType ?> letter</span></button>
				<?php }?>
			<?php }?>
			<button type="submit" class="auto classy green venti" value="submit" id="btn_schedule-now"><a href="<?php echo Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/schedule/'.$element->event_id)?>"><span class="button-span button-span-green">Schedule now</span></a></button>
		<?php }else{?>
			<?php if ($element->has_address) {?>
				<button type="submit" class="auto classy blue venti" value="submit" id="btn_print-letter"><span class="button-span">Print letter</span></button>
			<?php }else{?>
				<button type="submit" class="auto classy disabled venti" value="submit" disabled="disabled"><span class="button-span button-span-blue">Print letter</span></button>
			<?php }?>
			<button type="submit" class="auto classy green venti" value="submit" id="btn_reschedule-now"><a href="<?php echo Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/reschedule/'.$element->event_id)?>"><span class="button-span button-span-green">Reschedule now</span></a></button>
			<button type="submit" class="auto classy green venti" value="submit" id="btn_reschedule-later"><a href="<?php echo Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/rescheduleLater/'.$element->event_id)?>"><span class="button-span button-span-green">Reschedule later</span></a></button>
		<?php }?>
		<button type="submit" class="auto classy red venti" value="submit" id="btn_cancel-operation"><a href="<?php echo Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/default/cancel/'.$element->event_id)?>"><span class="button-span button-span-red">Cancel operation</span></a></button>
	</div>
<?php }?>
