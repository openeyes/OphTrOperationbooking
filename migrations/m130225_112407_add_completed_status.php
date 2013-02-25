<?php

class m130225_112407_add_completed_status extends CDbMigration
{
	public function up()
	{
		$this->insert('ophtroperation_operation_status',array('name'=>'Completed'));
	}

	public function down()
	{
		$this->delete('ophtroperation_operation_status',"name='Completed'");
	}
}
