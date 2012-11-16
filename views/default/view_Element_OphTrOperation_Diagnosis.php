
<h4 class="elementTypeName"><?php  echo $element->elementType->name ?></h4>

<div class="eventHighlight big">
	<?php $disorder = $element->disorder?>
	<h4><?php echo !empty($disorder) ? $element->eye->adjective : 'Unknown' ?> <?php echo !empty($disorder) ? $element->disorder->term : 'Unknown' ?></h4>
</div>
