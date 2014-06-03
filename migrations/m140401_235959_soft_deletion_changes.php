<?php

class m140401_235959_soft_deletion_changes extends OEMigration
{
	public function up()
	{
		$this->alterColumn('ophtroperationbooking_operation_erod', 'session_id', 'int unsigned null');

		$this->addColumn('ophtroperationbooking_operation_cancellation_reason', 'active', 'boolean not null default true');
		$this->addColumn('ophtroperationbooking_operation_ward', 'active', 'boolean not null default true');
		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options', 'active', 'boolean not null default true');

		$this->addColumn('ophtroperationbooking_operation_theatre', 'active', 'boolean not null default true');
		$this->update('ophtroperationbooking_operation_theatre', array('active' => new CDbExpression('not(deleted)')));
		$this->dropColumn('ophtroperationbooking_operation_theatre', 'deleted');
	}

	public function down()
	{
		$this->addColumn('ophtroperationbooking_operation_theatre', 'deleted', "tinyint(1) DEFAULT '0'");
		$this->update('ophtroperationbooking_operation_theatre', array('deleted' => new CDbExpression('not(active)')));
		$this->dropColumn('ophtroperationbooking_operation_theatre', 'active');

		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason','active');
		$this->dropColumn('ophtroperationbooking_operation_ward','active');
		$this->dropColumn('ophtroperationbooking_scheduleope_schedule_options','active');
	}
}
