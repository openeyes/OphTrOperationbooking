<?php

class m140804_075912_cancellation_reason_text_field extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason','text','name');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason_version','text','name');
	}

	public function down()
	{
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason','name','text');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason_version','name','text');
	}
}
