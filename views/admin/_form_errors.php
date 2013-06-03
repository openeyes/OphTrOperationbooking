<?php if (isset($errors) && !empty($errors)) {?>
	<div class="alertBox">
		<p>Please fix the following errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<?php foreach ($errs as $err) {?>
				<ul>
					<li>
						<?php echo $err?>
					</li>
				</ul>
			<?php }?>
		<?php }?>
	</div>
<?php }?>
