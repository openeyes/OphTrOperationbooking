<?php

class m131204_112818_rtt_comments extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationbooking_operation', 'comments_rtt', "TEXT NULL COLLATE 'utf8_bin'");
	}

	public function down()
	{

		$this->dropColumn('et_ophtroperationbooking_operation', 'comments_rtt');
	}
}
