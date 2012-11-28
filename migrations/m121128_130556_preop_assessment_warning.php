<?php

class m121128_130556_preop_assessment_warning extends CDbMigration
{
	public function up()
	{
		$this->createTable('ophtroperation_operation_preop_assessment_rule', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'parent_rule_id' => 'int(10) unsigned NULL',
				'rule_order' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'theatre_id' => 'int(10) unsigned NULL',
				'subspecialty_id' => 'int(10) unsigned NULL',
				'show_warning' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_par_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_par_cui_fk` (`created_user_id`)',
				'KEY `ophtroperation_operation_par_pri_fk` (`parent_rule_id`)',
				'KEY `ophtroperation_operation_par_ti_fk` (`theatre_id`)',
				'KEY `ophtroperation_operation_par_si_fk` (`subspecialty_id`)',
				'CONSTRAINT `ophtroperation_operation_par_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_par_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_par_pri_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperation_operation_preop_assessment_rule` (`id`)',
				'CONSTRAINT `ophtroperation_operation_par_ti_fk` FOREIGN KEY (`theatre_id`) REFERENCES `theatre` (`id`)',
				'CONSTRAINT `ophtroperation_operation_par_si_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('ophtroperation_operation_preop_assessment_rule');
	}
}
