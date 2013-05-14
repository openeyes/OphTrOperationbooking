<?php

class m130513_124510_rename_colliding_fields extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('ophtroperationbooking_operation_booking','cancellation_date','booking_cancellation_date');
		$this->renameColumn('et_ophtroperationbooking_operation','cancellation_date','operation_cancellation_date');
	}

	public function down()
	{
		$this->renameColumn('ophtroperationbooking_operation_booking','booking_cancellation_date','cancellation_date');
		$this->renameColumn('et_ophtroperationbooking_operation','operation_cancellation_date','cancellation_date');
	}
}
