<?php
	// Добавление прав на управление модулем
	$this->load->model('user/user_group');
	$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'module/vkm');
	$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'module/vkm');
	
	$r = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "vkm_log_export'");
	if (!$r->num_rows) {
		$this->db->query("CREATE TABLE `" . DB_PREFIX . "vkm_log_export` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`oc_product_id` INT(11) NOT NULL,
			`vk_product_id` INT(11) NOT NULL,
			`vk_market_id` INT(11) NOT NULL,
			`vk_category_id` INT(11) NOT NULL,
			`data_export` TEXT NOT NULL,
			`result` INT(11) NOT NULL,
			`date_export` DATETIME NOT NULL,
			`date_create` DATETIME NOT NULL,
			PRIMARY KEY (`id`),
			INDEX `oc_product_id` (`oc_product_id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=1
		;
		");
	}
		
		
	
	
	
	