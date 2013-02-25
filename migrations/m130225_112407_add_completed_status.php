<?php

class m130225_112407_add_completed_status extends CDbMigration
{
	public function up()
	{
		$this->insert('ophtroperationbooking_operation_status',array('name'=>'Completed'));
	}

	public function down()
	{
		$this->delete('ophtroperationbooking_operation_status',"name='Completed'");
	}
}
