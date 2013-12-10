<?php

class m131210_144551_soft_deletion extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_letter_contact_rule','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_letter_contact_rule_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_booking','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_booking_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_date_letter_sent','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_date_letter_sent_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod_rule','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod_rule_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod_rule_item','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_erod_rule_item_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_name_rule','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_name_rule_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_priority','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_priority_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_procedures_procedures','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_procedures_procedures_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_sequence_interval','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_sequence_interval_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_status','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_status_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_ward','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_ward_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_waiting_list_contact_rule','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_waiting_list_contact_rule_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule','deleted');
		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule_version','deleted');
		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule_type','deleted');
		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','deleted');
		$this->dropColumn('ophtroperationbooking_letter_contact_rule','deleted');
		$this->dropColumn('ophtroperationbooking_letter_contact_rule_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_booking','deleted');
		$this->dropColumn('ophtroperationbooking_operation_booking_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason','deleted');
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_date_letter_sent','deleted');
		$this->dropColumn('ophtroperationbooking_operation_date_letter_sent_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod_rule','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod_rule_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod_rule_item','deleted');
		$this->dropColumn('ophtroperationbooking_operation_erod_rule_item_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_name_rule','deleted');
		$this->dropColumn('ophtroperationbooking_operation_name_rule_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_priority','deleted');
		$this->dropColumn('ophtroperationbooking_operation_priority_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_procedures_procedures','deleted');
		$this->dropColumn('ophtroperationbooking_operation_procedures_procedures_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_sequence_interval','deleted');
		$this->dropColumn('ophtroperationbooking_operation_sequence_interval_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_status','deleted');
		$this->dropColumn('ophtroperationbooking_operation_status_version','deleted');
		$this->dropColumn('ophtroperationbooking_operation_ward','deleted');
		$this->dropColumn('ophtroperationbooking_operation_ward_version','deleted');
		$this->dropColumn('ophtroperationbooking_scheduleope_schedule_options','deleted');
		$this->dropColumn('ophtroperationbooking_scheduleope_schedule_options_version','deleted');
		$this->dropColumn('ophtroperationbooking_waiting_list_contact_rule','deleted');
		$this->dropColumn('ophtroperationbooking_waiting_list_contact_rule_version','deleted');
	}
}
