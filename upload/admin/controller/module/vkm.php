<?php

class ControllerModuleVKM extends Controller
	{
        private $error = array();
        private $arLogs = array();
		
		public function index() {
			
			$this->load->language('module/vkm');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('setting/setting');
			$data['token'] = $this->session->data['token'];
			foreach ($this->model_setting_setting->getSetting('vkm') as $k=>$v) {
				$data[$k] =$v;
			}
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				foreach ($this->request->post['vkm_group_name'] as $k=>$v) {
					if (!$v) {
						unset($this->request->post['vkm_group_name'][$k]);
						unset($this->request->post['vkm_group_id'][$k]);
					}
					
					if (!$this->request->post['vkm_group_id'][$k]) {
						unset($this->request->post['vkm_group_id'][$k]);
						unset($this->request->post['vkm_group_name'][$k]);
					}
				}
				
				$this->model_setting_setting->editSetting('vkm', $this->request->post);
	
				$this->session->data['success'] = $this->language->get('text_success');
	
				$this->response->redirect($this->url->link('extension/module', 'token=' . $data['token'], true));
			}
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_edit'] = $this->language->get('text_edit');
			$data['text_module'] = $this->language->get('text_module');
			$data['entry_status'] = $this->language->get('entry_status');
			$data['text_home'] = $this->language->get('text_home');
			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');
			

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}
			
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $data['token'], true)
			);
	
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_module'),
				'href' => $this->url->link('extension/module', 'token=' . $data['token'], true)
			);
	
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/vkm', 'token=' . $data['token'], true)
			);
	
			$data['action'] = $this->url->link('module/vkm', 'token=' . $data['token'], true);
			$data['cancel'] = $this->url->link('extension/module', 'token=' . $data['token'], true);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			
			$this->response->setOutput($this->load->view('module/vkm.tpl', $data));
		}
		
		
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'module/vkm')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
	
			return !$this->error;
		}
		
		public function getSettings() {
			$this->load->model('setting/setting');
			return $this->model_setting_setting->getSetting('vkm');
		}
		public function getExportInterface() {
			header('Content-Type: text/html; charset=UTF-8');
			
			$queryData = $this->request->post;
			
			$dataSettings = $this->getSettings();
			$html = '';
			if (!$dataSettings['vkm_app_id'] OR
					!$dataSettings['vkm_api_secret'] OR
					!$dataSettings['vkm_access_token'] OR
					!$dataSettings['vkm_group_id']) {
					$html .= '<p style="padding:15px;" class="bg-danger"><b>Проверьте настройки модуля</b></p>';
			}
			
			if ($queryData['product_id']) {
				$arProductsID = explode(',',$queryData['product_id']);
				
				$this->load->model('catalog/product');
				$html = '<form method="post" action="'.$this->url->link('module/vkm/exportProduct', 'token=' . $this->session->data['token'], true).'" id="export">';
				if (count($arProductsID) > 1) {
					$html .= '
<h3>Общие настройки</h3>
<div class="form-group">
	<label for="name">Куда выгружать товар</label>
	'.$this->getHTMLSelectOwner('main_owner_id', '').'

	<label for="name">В какую категорию выгружать товар</label>
	'.$this->getHTMLSelectCategory('main_category_id', '').'
</div>

<hr style="border-top: 1px solid #929191;">';
				}
				
				$htmlSelectOwner = $this->getHTMLSelectOwner('owner_id[]', (count($arProductsID) > 1)?true:false);
				$htmlSelectCategory = $this->getHTMLSelectCategory('category_id[]', (count($arProductsID) > 1)?true:false);
				
				foreach ($arProductsID as $k=>$productID) {
					if (!$productID) {
						unset($arProductsID[$k]);
						continue;
					}
					$product = $this->model_catalog_product->getProduct($productID);
				
				
					$html .= '<div><img src="/image/'.$product['image'].'" alt="" class="img-rounded" style="width:180px;">&nbsp;';
					foreach ($this->model_catalog_product->getProductImages($productID) as $v) {
						$html .= '<img src="/image/'.$v['image'].'" alt="" class="img-rounded" style="width:100px;">&nbsp;';
					}
					
					
					

					$html .= '
		<input name="product_id[]" type="hidden" value="'.$product['product_id'].'">
		<div class="form-group">
			<label for="name">Название товара</label>
			<input name="name[]" type="text" class="form-control name" value="'.$product['name'].'">
		</div>
		<div class="form-group">
			<label for="category_id">Категория размещения</label>
			'.$htmlSelectCategory.'
		</div>
		<div class="form-group">
			<label for="price">Цена</label>
			<input name="price[]" type="text" class="form-control price"value="'.$product['price'].'">
		</div>
		<div class="form-group">
			<label for="description">Описание</label>
			<textarea name="description[]" class="form-control" rows="8" placeholder="description">'.trim(strip_tags(htmlspecialchars_decode($product['description']))).'</textarea>
		</div>
		<div class="form-group">
			<label for="owner_id">Куда выгружать товар</label>
			'.$htmlSelectOwner.'
		</div>
	<br><hr style="border-top: 1px solid #929191;"><br></div>';
				}
			} else {}
			$html .= '</form>';
			echo $html;
		}
	
		public function exportProduct() {
			$this->load->model('catalog/product');
			
			$queryData = $this->request->post;
			$VKAPI = $this->getObjectAPIVK();
			
			$this->session->data['success'] = (isset($this->session->data['success']))?$this->session->data['success']:'';	
			$this->session->data['warning'] = (isset($this->session->data['warning']))?$this->session->data['warning']:'';	
			
			foreach ($queryData['product_id'] as $k=>$productID) {
				$product = $this->model_catalog_product->getProduct($productID);
				
				$i = 0;
				$arProductPhotos = '';
				if ($product['image']) {
					$arProductPhotos[$i]['path'] = $_SERVER['DOCUMENT_ROOT'] . '/image/'.$product['image'];
					$arProductPhotos[$i]['is_main'] = 1;
				}
				
				foreach ($this->model_catalog_product->getProductImages($productID) as $v) {
					$i++;
					$arProductPhotos[$i]['path'] = $_SERVER['DOCUMENT_ROOT'] . '/image/' . $v['image'];
					$arProductPhotos[$i]['is_main'] = 0;
					
				}
				
				if ($queryData['owner_id'][$k]) {
					$IDMarket = $queryData['owner_id'][$k];
				} else {
					$IDMarket = $queryData['main_owner_id'];
				}
				
				if ($queryData['category_id'][$k]) {
					$IDCategory = $queryData['category_id'][$k];
				} else {
					$IDCategory = $queryData['main_category_id'];
				}
				
				
				
				$VKAPI->setIDMarket($IDMarket);
				
				$dataExport = array('product' => array(
									'name' => $queryData['name'][$k],
									'description' => $queryData['description'][$k],
									'category_id' => $IDCategory, 
									'price' => $queryData['price'][$k],
									'deleted' => '0'),
									'photos' => $arProductPhotos);
				$r = $VKAPI->add($dataExport);
				
				
				
				if ($r) {

					$this->db->query("INSERT INTO `" . DB_PREFIX . "vkm_log_export` (`oc_product_id`,
																					`vk_product_id`,
																					`vk_market_id`,
																					`vk_category_id`,
																					`result`,
																					`date_export`,
																					`date_create`)
																					VALUES
																					('{$productID}',
																					'{$r}',
																					'{$IDMarket}',
																					'{$IDCategory}',
																					'1',
																					NOW(),
																					NOW())");
					
					$this->session->data['success'] .= 'Товар экспортирован. <a href="https://vk.com/club'.$VKAPI->marketID.'?w=product-'.$VKAPI->marketID.'_'.$r.'">' . $queryData['name'][$k] . ' (' . $r . ')</a><br>';
				} else {
					$this->session->data['warning'] .= 'Не удалось экспортировать товар. <a href="/admin/index.php?route=catalog/product/edit&token=' . $this->session->data['token'] . '&product_id='.$productID.'">' . $queryData['name'][$k] . ' (' . $productID . ')</a><br>';
				}
			}
			$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'], true));
		}
		
		private function getHTMLSelectOwner($name, $firstClear) {
			$dataSettings = $this->getSettings();

			$htmlSelectOwner = '<select class="form-control" name="'.$name.'">';
			if ($firstClear) {
				$htmlSelectOwner .= '<option></option>';
			}			
			foreach ($dataSettings['vkm_group_name'] as $k=>$v) {
				$htmlSelectOwner .= '<option value="'.$dataSettings['vkm_group_id'][$k].'">'.$v.'</option>';
			}
			$htmlSelectOwner .= '</select>';
			return $htmlSelectOwner;	
		}
		private function getHTMLSelectCategory($name, $firstClear) {
			$VKAPI = $this->getObjectAPIVK();
			
			$htmlSelectCategory = '<select class="form-control" name="'.$name.'">';
			if ($firstClear) {
				$htmlSelectCategory .= '<option></option>';
			}	
			foreach ($VKAPI->getCategoriesTree() as $v) {
				if ($v['childrens']) {
					$htmlSelectCategory .= '<optgroup label="'.$v['name'].'">';
					foreach ($v['childrens'] as $vv) {
						$htmlSelectCategory .= '<option value="'.$vv['id'].'">'.$vv['name'].'</option>';
					}
					$htmlSelectCategory .= '</optgroup>';
				} else {
					$htmlSelectCategory .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
				}
			}
			$htmlSelectCategory .= '</select>';
			
			
			
			
			return $htmlSelectCategory;	
		}
		
		public function getObjectAPIVK() {
			$dataSettings = $this->getSettings();
			include_once DIR_SYSTEM. "library/vk-market.php";
			if ($dataSettings['vkm_app_id'] AND
				$dataSettings['vkm_api_secret'] AND
				$dataSettings['vkm_access_token'] AND
				$dataSettings['vkm_group_id']) {
					$vkMarket = new VKMarket($dataSettings['vkm_app_id'], $dataSettings['vkm_api_secret'], $dataSettings['vkm_access_token']);
					$vkMarket->setApiVersion('5.52');
					//$vkMarket->setIDMarket($dataSettings['vkm_group_id']);
					
			} else {
				
				return false;
			}
			
			return $vkMarket;
		}
		
		public function getExportList() {
			$r = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vkm_log_export` WHERE `oc_product_id` = '" . $this->request->request['oc_product_id'] . "'"); 
			
			if ($this->request->request['format'] == 'json') {
				echo json_encode($r->rows);
			} else {
				echo $r->rows;
			}
		}
		
		public function delete() {
			if ($this->request->request['id']) {
				$r = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vkm_log_export` WHERE `id` = '" . $this->request->request['id'] . "'");
				
				$vkMarket = $this->getObjectAPIVK();
				$vkMarket->setIDMarket($r->row['vk_market_id']);
				$vkMarket->delete(array(array('product_id' => $r->row['vk_product_id'],
											  'market_id' => $r->row['vk_market_id'])));
				
				$this->db->query("DELETE FROM `" . DB_PREFIX . "vkm_log_export` WHERE `id` = '" . $this->request->request['id'] . "'"); 
				echo 1;
			}
		}
	}