<?php

class m130423_163100_fix_theatre_sort extends OEMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_theatre', 'display_order', 'int(10) NOT NULL DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('ophtroperationbooking_operation_theatre', 'display_order');
	}

}
