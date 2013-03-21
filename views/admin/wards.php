<div class="report curvybox white">
	<h3 class="georgia">Wards</h3>
	<div>
		<form id="wards">
			<ul class="grid reduceheight">
				<li class="header">
					<span class="column_id">ID</span>
					<span class="column_code">Code</span>
					<span class="column_name">Name</span>
					<span class="column_long_name">Long name</span>
					<span class="column_restriction">Restrictions</span>
				</li>
				<div class="sortable">
					<?php
					$criteria = new CDbCriteria;
					$criteria->order = "id asc";
					foreach (OphTrOperationbooking_Operation_Ward::model()->findAll($criteria) as $i => $ward) {?>
						<li class="<?php if ($i%2 == 0) {?>even<?php }else{?>odd<?php }?>" data-attr-id="<?php echo $ward->id?>">
							<span class="column_id"><?php echo $ward->id?></span>
							<span class="column_code"><?php echo $ward->code?></span>
							<span class="column_name"><?php echo $ward->name?></span>
							<span class="column_long_name"><?php echo $ward->long_name?>&nbsp;</span>
							<span class="column_restriction"><?php echo $ward->restriction?></span>
							<span class="column_deleted"><a class="deleteDrugItem" href="#" rel="<?php echo $ward->id?>">delete</a></span>
						</li>
					<?php }?>
				</div>
			</ul>
		</form>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add', array('colour' => 'blue'))->toHtml()?>
</div>
