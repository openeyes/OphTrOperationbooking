<?php

class m140116_083623_default_admission_time_version extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_sequence_version','default_admission_time','time not null');
		$this->addColumn('ophtroperationbooking_operation_session_version','default_admission_time','time not null');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_operation_sequence_version','default_admission_time');
		$this->dropColumn('ophtroperationbooking_operation_session_version','default_admission_time');
	}
}
