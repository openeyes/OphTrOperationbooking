<?php

class m130621_105848_soft_deletion_of_sequences_and_sessions extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_sequence','deleted','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('ophtroperationbooking_operation_session','deleted','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_operation_session','deleted');
		$this->dropColumn('ophtroperationbooking_operation_sequence','deleted');
	}
}
