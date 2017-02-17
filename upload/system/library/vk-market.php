<?php
	include_once DIR_SYSTEM . "/library/vk.php";
	
	class VKMarket extends VK\VK {
		public $marketID;
		public $arLog;
		private $arCategories = array();

		public function validateImage($path) {
			if(is_file($path)) {
				$r = getimagesize($path);
				if($r[0] >= 400 AND $r[1] >= 400) {
					return true;
				} else {
					$this->arLog[] = array('method' => 'validateImage',
										   'if' => 'width >= 400 AND height >= 400');
					return false;
				}
			}
		}

		
		public function setIDMarket($id) {
			$this->marketID = $id;
		}
		
		/**
		 * Получаем ссылку для загрузки изображения
		 *
		 * @link https://vk.com/dev/photos.getMarketUploadServer
		 * @return string Возвращает адрес сервера для загрузки фотографии товара.
		 * 
		 */
		
		public function getMarketUploadServer($params) {
			$r = $this->api('photos.getMarketUploadServer', $params, 'array', 'post');
			return $r['response']['upload_url'];
		}
		
		/**
		 * Получаем список категорий.
		 *
		 * @param $offset смещение относительно первого элемента.
		 *
		 * @link https://vk.com/dev/market.getCategories
		 *
		 * @return array массив категорий
		 */
		public function getCategories($offset = 0, $key = false) {
			if (count($this->arCategories) AND !$key) {
				return $this->arCategories;
			} else {
				if ($offset) {
					$arParams['offset'] = $offset;
				}
				$arParams['count'] = '1000';
				$r = $this->api('market.getCategories', $arParams, 'array', 'post');
				
				$this->arCategories = $r['response']['items'];
				
				if ($key) {
					foreach ($r['response']['items'] as $v) {
						$d[$v['id']] = $v; 
					}
					return $d;
				} 
				
				return $r['response']['items'];
			}
		}
		
		/**
		 * Получаем список категорий. И формируем массив для рекурсивного обхода.
		 *
		 * @param $offset смещение относительно первого элемента.
		 */
		public function getCategoriesTree($offset = 0)
		{
			if (count($this->arCategories)) {
				$r = $this->arCategories;
			} else {
				$r = $this->getCategories($offset);
			}
			
			foreach ($r as $v) {
				//секция
				$arCategories[$v['section']['id']]['id'] 	= $v['section']['id'];
				$arCategories[$v['section']['id']]['name'] 	= $v['section']['name'];
				//категории
				$arCategories[$v['section']['id']]['childrens'][$v['id']]['id'] 	= $v['id'];
				$arCategories[$v['section']['id']]['childrens'][$v['id']]['name'] 	= $v['name'];
			}
			
			return $arCategories;
		}

		/**
		 * Сохраняет фотографии после успешной загрузки на URI
		 * @link https://vk.com/dev/photos.saveMarketPhoto
		 * 
		 */
		public function saveMarketPhoto($photo, $params) {
			//поддержка php < 5.5
			if (!function_exists('curl_file_create')) {
				function curl_file_create($filename, $mimetype = '', $postname = '') {
					if(file_exists($filename)) {
						return "@$filename;filename="
						. ($postname ?: basename($filename))
						. ($mimetype ? ";type=$mimetype" : '');
					}
				}
			}
			
			//проверяем подходит ли фотка. если не подходим меняем на заглушку
			$photo['path'] = ($this->validateImage($photo['path']))?$photo['path']:$_SERVER['DOCUMENT_ROOT'].'/image/catalog/def_img.jpg';
			
			//надо уточнить. для каждой ли загрузки нужно получать ссылку
			$urlUpload = $this->getMarketUploadServer($params);
			$p = getimagesize($photo['path']);
			$photoData = $this->request($urlUpload,
						    'POST',
						    array('file' => curl_file_create($photo['path'], $p['mime'], $photo['name'])));
				
			$photoData = json_decode($photoData, true);
			
			$photoData['crop_data'] = (isset($photoData['crop_data']))?$photoData['crop_data']:'';
			$photoData['crop_hash'] = (isset($photoData['crop_hash']))?$photoData['crop_hash']:'';
			
			return $this->api('photos.saveMarketPhoto',
					  array('group_id' => $this->marketID,
						'photo' => $photoData['photo'],
						'server' => $photoData['server'],
						'hash' => $photoData['hash'],
						'crop_data' => $photoData['crop_data'],
						'crop_hash' => $photoData['crop_hash']), 'array', 'post');
		}
		
		public function delete($arProducts) {
			foreach($arProducts as $v) {
				$r = $this->api('market.delete',array(
					'owner_id' => '-'.$v['market_id'],
					'item_id' => $v['product_id']), 'array', 'post');
				
				$arResult[$v['product_id']]['product_id'] = $v['product_id'];
				$arResult[$v['product_id']]['is_deleted'] = $r['response'];
			}
			
			return $arResult;
		}
		
		public function add($params) {
			
			if ($params['product'] AND $params['photos']) {
				
				foreach ($params['photos'] as $v) {
					
					$r = $this->saveMarketPhoto(array('path' => $v['path'],
														'name' => 'q.JPG'),
						array('group_id' => $this->marketID,
							'main_photo' => $v['is_main'],
							/*'crop_x' => '400',
							'crop_y' => '400',
							'crop_width' => '400'*/
							));
					$r = (isset($r['response'][0]))?$r['response'][0]:'';
					$mainPhoto = ($v['is_main'] AND !isset($mainPhoto))?$r['id']:$mainPhoto;
					
					$arrPhotoID[] = $r['id'];
					
					
				}
				
				$r = $this->api('market.add',array(
						'owner_id' 		=> '-'.$this->marketID,
						'name' 			=> $params['product']['name'],
						'description' 	=> $params['product']['description'],
						'category_id' 	=> $params['product']['category_id'],
						'price' 		=> $params['product']['price'],
						'deleted' 		=> '0',
						'main_photo_id' => $mainPhoto,
						'photo_ids' 	=> (count($arrPhotoID))?implode(',', $arrPhotoID):''), 'array', 'post');
				
				return (isset($r['response']['market_item_id']))?$r['response']['market_item_id']:'';
			}
		}
	
		public function getAlbums ($marketID) {
			if (!$marketID) {
				$marketID = $this->marketID;
			}
			
			$r = $this->api('market.getAlbums',array(
						'owner_id'	=> '-'.$marketID,
						'count' 	=> 100), 'array', 'post');
				
			return (isset($r['response']['items']))?$r['response']['items']:'';
		}
		
		public function addToAlbum($ownerID, $itemID, $albumIDs) {
			$r = $this->api('market.addToAlbum',array(
						'owner_id'	=> '-'.$ownerID,
						'item_id' 	=> $itemID,
						'album_ids' => $albumIDs), 'array', 'post');
		}
