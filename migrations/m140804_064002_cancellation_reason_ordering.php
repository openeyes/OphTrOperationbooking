<?php

class m140804_064002_cancellation_reason_ordering extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason','order','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','order','tinyint(1) unsigned not null');
		$this->dbConnection->createCommand("update ophtroperationbooking_operation_cancellation_reason set `order` = id")->execute();
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason','order');
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason_version','order');
	}
}
