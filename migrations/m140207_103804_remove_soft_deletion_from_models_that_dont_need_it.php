<?php

class m140207_103804_remove_soft_deletion_from_models_that_dont_need_it extends CDbMigration
{
	public $tables = array(
		'et_ophtroperationbooking_diagnosis',
		'et_ophtroperationbooking_operation',
		'et_ophtroperationbooking_scheduleope',
		'ophtroperationbooking_admission_letter_warning_rule',
		'ophtroperationbooking_letter_contact_rule',
		'ophtroperationbooking_operation_booking',
		'ophtroperationbooking_operation_date_letter_sent',
		'ophtroperationbooking_operation_erod',
		'ophtroperationbooking_operation_erod_rule',
		'ophtroperationbooking_operation_erod_rule_item',
		'ophtroperationbooking_operation_name_rule',
		'ophtroperationbooking_operation_procedures_procedures',
		'ophtroperationbooking_operation_sequence',
		'ophtroperationbooking_operation_sequence_interval',
		'ophtroperationbooking_operation_session',
		'ophtroperationbooking_operation_status',
		'ophtroperationbooking_waiting_list_contact_rule',
	);

	public function up()
	{
		foreach ($this->tables as $table) {
			$this->dropColumn($table,'deleted');
			$this->dropColumn($table.'_version','deleted');

			$this->dropForeignKey("{$table}_aid_fk",$table."_version");
		}
	}

	public function down()
	{
		foreach ($this->tables as $table) {
			$this->addColumn($table,'deleted','tinyint(1) unsigned not null');
			$this->addColumn($table."_version",'deleted','tinyint(1) unsigned not null');

			$this->addForeignKey("{$table}_aid_fk",$table."_version","id",$table,"id");
		}
	}
}
