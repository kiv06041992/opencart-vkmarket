<?php
	// Добавление прав на управление модулем
	$this->load->model('user/user_group');
	$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'module/vkm');
	$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'module/vkm');
	
	$r = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "vkm_log_export'");
	if (!$r->num_rows) {
		createrTableLog($this, DB_PREFIX);
	} else {
		$tablePref = rand() . '_';
		$this->db->query('CREATE TABLE `'.$tablePref.'vkm_log_export`
							SELECT *
							FROM `' . DB_PREFIX . 'vkm_log_export`;');
		$this->db->query('DROP TABLE `' . DB_PREFIX . 'vkm_log_export`;');
		
						
		createrTableLog($this, DB_PREFIX);
		$r = $this->db->query('SHOW COLUMNS FROM `'.$tablePref.'vkm_log_export`');
		foreach ($r->rows as $v) {
			$arRows[$v['Field']] = $v['Field'];
		}
		$this->db->query('INSERT INTO `' . DB_PREFIX . 'vkm_log_export` ('.implode(',', $arRows).')
							SELECT '.implode(',', $arRows).' FROM `'.$tablePref.'vkm_log_export`;');
		
		$this->db->query('DROP TABLE `' . $tablePref . 'vkm_log_export`;');
	}
		
	
	function createrTableLog($obj, $pref)  {
		$obj->db->query('CREATE TABLE `' . $pref . 'vkm_log_export` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`oc_product_id` INT(11) NOT NULL,
			`vk_product_id` INT(11) NOT NULL,
			`vk_market_id` INT(11) NOT NULL,
			`vk_category_id` INT(11) NOT NULL,
			`vk_album_ids` INT(11) NULL DEFAULT NULL,
			`data_export` TEXT NOT NULL,
			`result` INT(11) NOT NULL,
			`date_export` DATETIME NOT NULL,
			`date_create` DATETIME NOT NULL,
			
			PRIMARY KEY (`id`),
			INDEX `oc_product_id` (`oc_product_id`)
		)
		COLLATE="utf8_general_ci"
		ENGINE=InnoDB
		AUTO_INCREMENT=1
		;
		');
	}
	
	
	
	