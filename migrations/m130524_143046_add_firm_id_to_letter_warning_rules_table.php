<?php

class m130524_143046_add_firm_id_to_letter_warning_rules_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule','firm_id','int(10) unsigned NULL');
		$this->createIndex('ophtroperationbooking_alw_rule_fidfk','ophtroperationbooking_admission_letter_warning_rule','firm_id');
		$this->addForeignKey('ophtroperationbooking_alw_rule_fidfk','ophtroperationbooking_admission_letter_warning_rule','firm_id','firm','id');
	}

	public function down()
	{
		$this->dropForeignKey('ophtroperationbooking_alw_rule_fidfk','ophtroperationbooking_admission_letter_warning_rule');
		$this->dropIndex('ophtroperationbooking_alw_rule_fidfk','ophtroperationbooking_admission_letter_warning_rule');
		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule','firm_id');
	}
}
