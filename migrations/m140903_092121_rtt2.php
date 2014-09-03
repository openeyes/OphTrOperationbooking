<?php

class m140903_092121_rtt2 extends OEMigration
{
	public function safeUp()
	{
		$this->addColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id', 'integer unsigned');
	}

	public function safeDown()
	{
		$this->dropColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id');
	}
}
