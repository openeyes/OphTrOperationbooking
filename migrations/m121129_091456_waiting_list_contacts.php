<?php

class m121129_091456_waiting_list_contacts extends CDbMigration
{
	public function up()
	{
		$this->createTable('ophtroperationbooking_waiting_list_contact_rule', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'parent_rule_id' => 'int(10) unsigned NULL',
				'rule_order' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'site_id' => 'int(10) unsigned NULL',
				'service_id' => 'int(10) unsigned NULL',
				'firm_id' => 'int(10) unsigned NULL',
				'is_child' => 'tinyint(1) unsigned NULL',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'telephone' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` (`parent_rule_id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_site_id_fk` (`site_id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_service_id_fk` (`service_id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_firm_id_fk` (`firm_id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperationbooking_waiting_list_cr_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_waiting_list_contact_rule` (`id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_service_id_fk` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperationbooking_waiting_list_cr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('ophtroperationbooking_waiting_list_contact_rule');
	}
}
