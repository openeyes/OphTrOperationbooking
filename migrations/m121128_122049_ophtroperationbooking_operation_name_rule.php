<?php

class m121128_122049_ophtroperationbooking_operation_name_rule extends CDbMigration
{
	public function up()
	{
		$this->createTable('ophtroperationbooking_operation_name_rule', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'theatre_id' => 'int(10) unsigned NULL',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperationbooking_operation_name_rt_id_fk` (`theatre_id`)',
				'KEY `ophtroperationbooking_operation_name_r_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperationbooking_operation_name_r_cid_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperationbooking_operation_name_rt_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)',
				'CONSTRAINT `ophtroperationbooking_operation_name_r_cid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperationbooking_operation_name_r_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('ophtroperationbooking_operation_name_rule');
	}
}
