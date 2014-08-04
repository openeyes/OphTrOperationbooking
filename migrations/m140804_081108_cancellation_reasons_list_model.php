<?php

class m140804_081108_cancellation_reasons_list_model extends OEMigration
{
	public function up()
	{
		$this->createTable('ophtroperationnote_operation_cancellation_reason_list', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) not null',
				'display_order' => 'tinyint(1) unsigned not null',
				'comments' => 'varchar(4096) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperationnote_operation_cancellation_reason_list_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperationnote_operation_cancellation_reason_list_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperationnote_operation_cancellation_reason_list_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperationnote_operation_cancellation_reason_list_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->versionExistingTable('ophtroperationnote_operation_cancellation_reason_list');

		$this->initialiseData(dirname(__FILE__));

		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason','list_no','int(10) unsigned not null');
		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason_version','list_no','int(10) unsigned not null');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason','list_no','list_id');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason_version','list_no','list_id');
		$this->addForeignKey('ophtroperationbooking_operation_cancellation_reason_list_id_fk','ophtroperationbooking_operation_cancellation_reason','list_id','ophtroperationnote_operation_cancellation_reason_list','id');

		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason','parent_id');
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason_version','parent_id');
	}

	public function down()
	{
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason','parent_id','int(10) unsigned null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','parent_id','int(10) unsigned null');

		$this->dropForeignKey('ophtroperationbooking_operation_cancellation_reason_list_id_fk','ophtroperationbooking_operation_cancellation_reason');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason','list_id','list_no');
		$this->renameColumn('ophtroperationbooking_operation_cancellation_reason_version','list_id','list_no');
		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason','list_no','tinyint(2) unsigned not null');
		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason_version','list_no','tinyint(2) unsigned not null');

		$this->dropTable('ophtroperationnote_operation_cancellation_reason_list_version');
		$this->dropTable('ophtroperationnote_operation_cancellation_reason_list');
	}
}
