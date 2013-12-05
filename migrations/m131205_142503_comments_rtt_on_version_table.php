<?php

class m131205_142503_comments_rtt_on_version_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationbooking_operation_version', 'comments_rtt', "TEXT NULL COLLATE 'utf8_bin'");
	}

	public function down()
	{

		$this->dropColumn('et_ophtroperationbooking_operation_version', 'comments_rtt');
	}
}
