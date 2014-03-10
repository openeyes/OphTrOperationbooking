<?php

class m140224_134157_transactions extends CDbMigration
{
	public $tables = array('et_ophtroperationbooking_diagnosis','et_ophtroperationbooking_operation','et_ophtroperationbooking_scheduleope','ophtroperationbooking_admission_letter_warning_rule_type','ophtroperationbooking_admission_letter_warning_rule','ophtroperationbooking_letter_contact_rule','ophtroperationbooking_operation_booking','ophtroperationbooking_operation_cancellation_reason','ophtroperationbooking_operation_date_letter_sent','ophtroperationbooking_operation_erod_rule_item','ophtroperationbooking_operation_erod_rule','ophtroperationbooking_operation_erod','ophtroperationbooking_operation_name_rule','ophtroperationbooking_operation_priority','ophtroperationbooking_operation_procedures_procedures','ophtroperationbooking_operation_sequence_interval','ophtroperationbooking_operation_sequence','ophtroperationbooking_operation_session','ophtroperationbooking_operation_status','ophtroperationbooking_operation_theatre','ophtroperationbooking_operation_ward','ophtroperationbooking_scheduleope_schedule_options','ophtroperationbooking_waiting_list_contact_rule');

	public function up()
	{
		foreach ($this->tables as $table) {
			$this->addColumn($table,'hash','varchar(40) not null');
			$this->addColumn($table,'transaction_id','int(10) unsigned null');
			$this->createIndex($table.'_tid',$table,'transaction_id');
			$this->addForeignKey($table.'_tid',$table,'transaction_id','transaction','id');
			$this->addColumn($table,'conflicted','tinyint(1) unsigned not null');

			$this->addColumn($table.'_version','hash','varchar(40) not null');
			$this->addColumn($table.'_version','transaction_id','int(10) unsigned null');
			$this->addColumn($table.'_version','deleted_transaction_id','int(10) unsigned null');
			$this->createIndex($table.'_vtid',$table.'_version','transaction_id');
			$this->addForeignKey($table.'_vtid',$table.'_version','transaction_id','transaction','id');
			$this->createIndex($table.'_dtid',$table.'_version','deleted_transaction_id');
			$this->addForeignKey($table.'_dtid',$table.'_version','deleted_transaction_id','transaction','id');
			$this->addColumn($table.'_version','conflicted','tinyint(1) unsigned not null');
		}
	}

	public function down()
	{
		foreach ($this->tables as $table) {
			$this->dropColumn($table,'hash');
			$this->dropForeignKey($table.'_tid',$table);
			$this->dropIndex($table.'_tid',$table);
			$this->dropColumn($table,'transaction_id');
			$this->dropColumn($table,'conflicted');

			$this->dropColumn($table.'_version','hash');
			$this->dropForeignKey($table.'_vtid',$table.'_version');
			$this->dropIndex($table.'_vtid',$table.'_version');
			$this->dropColumn($table.'_version','transaction_id');
			$this->dropForeignKey($table.'_dtid',$table.'_version');
			$this->dropIndex($table.'_dtid',$table.'_version');
			$this->dropColumn($table.'_version','deleted_transaction_id');
			$this->dropColumn($table.'_version','conflicted');
		}
	}
}
