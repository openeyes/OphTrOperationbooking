<?php

class m121128_150949_admission_letter_warning_rules extends CDbMigration
{
	public function up()
	{
		$this->createTable('ophtroperation_admission_letter_warning_rule_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_admission_letter_wrt_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_admission_letter_wrt_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_admission_letter_wrt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_admission_letter_wrt_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_admission_letter_warning_rule', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'rule_type_id' => 'int(10) unsigned NOT NULL',
				'parent_rule_id' => 'int(10) unsigned NULL',
				'rule_order' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'site_id' => 'int(10) unsigned NULL',
				'theatre_id' => 'int(10) unsigned NULL',
				'subspecialty_id' => 'int(10) unsigned NULL',
				'is_child' => 'tinyint(1) unsigned NULL DEFAULT NULL',
				'show_warning' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
				'warning_text' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'emphasis' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'strong' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_admission_lwr_rti_fk` (`rule_type_id`)',
				'KEY `ophtroperation_admission_lwr_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_admission_lwr_cui_fk` (`created_user_id`)',
				'KEY `ophtroperation_admission_lwr_pri_fk` (`parent_rule_id`)',
				'KEY `ophtroperation_admission_lwr_ti_fk` (`theatre_id`)',
				'KEY `ophtroperation_admission_lwr_si_fk` (`subspecialty_id`)',
				'KEY `ophtroperation_admission_lwr_site_fk` (`site_id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_rti_fk` FOREIGN KEY (`rule_type_id`) REFERENCES `ophtroperation_admission_letter_warning_rule_type` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_pri_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperation_admission_letter_warning_rule` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_ti_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperation_operation_theatre` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_si_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `ophtroperation_admission_lwr_site_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('ophtroperation_admission_letter_warning_rule');
		$this->dropTable('ophtroperation_admission_letter_warning_rule_type');
	}
}
