<div class="report curvybox white">
	<div class="reportInputs">
		<h3 class="georgia">Post-operative drugs</h3>
		<div>
			<form id="drugs">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_name">Name</span>
					</li>
					<div class="sortable">
						<?php
						$criteria = new CDbCriteria;
						$criteria->compare('deleted',0);
						$criteria->order = "display_order asc";
						foreach (PostopDrug::model()->findAll($criteria) as $i => $drug) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $drug->id?>">
								<span class="column_name"><a class="drugItem" href="#" rel="<?php echo $drug->id?>"><?php echo $drug->name?></a></span>
								<span class="column_deleted"><a class="deleteDrugItem" href="#" rel="<?php echo $drug->id?>">delete</a></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add', array('colour' => 'blue'))->toHtml()?>
</div>
