<?php
namespace Opencart\Application\Controller\Common;
class Header extends \Opencart\System\Engine\Controller {
	public function index() {
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['title'] = $this->document->getTitle();
		$data['base'] = HTTP_SERVER;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		// Hard coding css so they can be replaced via the events system.
		$data['bootstrap_css'] = 'view/stylesheet/bootstrap.css';
		$data['icons'] = 'view/javascript/fontawesome/css/fontawesome-all.min.css';
		$data['stylesheet'] = 'view/stylesheet/stylesheet.css';

		// Hard coding scripts so they can be replaced via the events system.
		$data['jquery'] = 'view/javascript/jquery/jquery-3.5.1.min.js';
		$data['bootstrap_js'] = 'view/javascript/bootstrap/js/bootstrap.bundle.min.js';

		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();

		$this->load->language('common/header');
		
		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/login');
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
			$data['profile'] = $this->url->link('common/profile', 'user_token=' . $this->session->data['user_token']);
			$data['logout'] = $this->url->link('common/logout', 'user_token=' . $this->session->data['user_token']);

			// Notifications
			$data['notifications'] = [];

			/*
			$this->load->model('setting/notification');

			$data['notification_total'] = count($this->model_setting_notification->getTotalNotifications());

			$results = $this->model_setting_notification->getNotifications();

			foreach ($results as $result) {
				$data['notifications'][] = [
					'title' => $result['title'],
					'href'  => $this->url->link('tool/notification|info', 'user_token=' . $this->session->data['user_token'] . '&notification_id=' . $result['notification_id'])
				];
			}
			*/

			$data['notification_all'] = $this->url->link('tool/notification', 'user_token=' . $this->session->data['user_token']);

			$this->load->model('tool/image');

			$data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);

			$this->load->model('user/user');
	
			$user_info = $this->model_user_user->getUser($this->user->getId());
	
			if ($user_info) {
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['username']  = $user_info['username'];
				$data['user_group'] = $user_info['user_group'];
	
				if (is_file(DIR_IMAGE . html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'))) {
					$data['image'] = $this->model_tool_image->resize(html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'), 45, 45);
				}
			}  else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['user_group'] = '';
			}
			
			// Stores
			$data['stores'] = [];

			$data['stores'][] = [
				'name' => $this->config->get('config_name'),
				'href' => HTTP_CATALOG
			];

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$data['stores'][] = [
					'name' => $result['name'],
					'href' => $result['url']
				];
			}
		}

		return $this->load->view('common/header', $data);
	}
}
