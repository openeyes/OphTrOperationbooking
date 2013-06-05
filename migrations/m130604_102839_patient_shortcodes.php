<?php

class m130604_102839_patient_shortcodes extends CDbMigration
{
	public function up()
	{
		$event_type = EventType::model()->find('class_name=?',array('OphTrOperationbooking'));

		$event_type->registerShortcode('opl','getLetterProcedures','Operations listed for');
		$event_type->registerShortcode('adm','getAdmissionDate','Admission date for most recent booking');
	}

	public function down()
	{
		$event_type = EventType::model()->find('class_name=?',array('OphTrOperationbooking'));

		$this->delete('patient_shortcode','event_type_id=:etid',array(':etid'=>$event_type->id));
	}
}
