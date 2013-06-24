<?php

class m130621_153035_soft_deletion_of_theatres extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_theatre','deleted','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_operation_theatre','deleted');
	}
}
