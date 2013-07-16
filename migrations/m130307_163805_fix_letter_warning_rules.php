<?php

class m130307_163805_fix_letter_warning_rules extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule','warning_text','text not null');
		$this->update('ophtroperationbooking_admission_letter_warning_rule', array('warning_text' => 'You may be given a prescription after your treatment. This can be collected from our pharmacy on the ward, however unless you have an exemption certificate the standard prescription charge will apply. Please ensure you, or the person collecting you, have the correct money to cover the prescription cost.'), "id = 7");

	}

	public function down()
	{
		$this->update('ophtroperationbooking_admission_letter_warning_rule', array('warning_text' => 'You may be given a prescription after your treatment. This can be collected from our pharmacy on the ward, however unless you have an exemption certificate the standard prescription charge will apply.	Please ensure you have the correct money or ask the re'), "id = 7" );
		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule','warning_text','string');
	}
}
