<?php

class m130531_140931_letter_contact_rules_is_child extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_letter_contact_rule','is_child','tinyint(1) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_letter_contact_rule','is_child');
	}
}
