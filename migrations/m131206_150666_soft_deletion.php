<?php

class m131206_150666_soft_deletion extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationbooking_diagnosis','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationbooking_diagnosis_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationbooking_operation','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationbooking_operation_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationbooking_scheduleope','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophtroperationbooking_scheduleope_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationbooking_diagnosis','deleted');
		$this->dropColumn('et_ophtroperationbooking_diagnosis_version','deleted');
		$this->dropColumn('et_ophtroperationbooking_operation','deleted');
		$this->dropColumn('et_ophtroperationbooking_operation_version','deleted');
		$this->dropColumn('et_ophtroperationbooking_scheduleope','deleted');
		$this->dropColumn('et_ophtroperationbooking_scheduleope_version','deleted');
	}
}
