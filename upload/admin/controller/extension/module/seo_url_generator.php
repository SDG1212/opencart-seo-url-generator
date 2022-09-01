<?php
class ControllerExtensionModuleSeoUrlGenerator extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/seo_url_generator');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_seo_url_generator', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/seo_url_generator', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/seo_url_generator', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_seo_url_generator_separator'])) {
			$data['module_seo_url_generator_separator'] = $this->request->post['module_seo_url_generator_separator'];
		} else {
			$data['module_seo_url_generator_separator'] = $this->config->get('module_seo_url_generator_separator');
		}

		if (isset($this->request->post['module_seo_url_generator_separator'])) {
			$data['module_seo_url_generator_separator'] = $this->request->post['module_seo_url_generator_separator'];
		} else {
			$data['module_seo_url_generator_separator'] = $this->config->get('module_seo_url_generator_separator');
		}

		if (isset($this->request->post['module_seo_url_generator_status'])) {
			$data['module_seo_url_generator_status'] = $this->request->post['module_seo_url_generator_status'];
		} else {
			$data['module_seo_url_generator_status'] = $this->config->get('module_seo_url_generator_status');
		}

		if (isset($this->request->post['module_seo_url_generator_donate'])) {
			$data['module_seo_url_generator_donate'] = $this->request->post['module_seo_url_generator_donate'];
		} elseif ($this->config->has('module_seo_url_generator_donate')) {
			$data['module_seo_url_generator_donate'] = $this->config->get('module_seo_url_generator_donate');
		} else {
			$data['module_seo_url_generator_donate'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seo_url_generator', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seo_url_generator')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function generate() {
		$json['result'] = array();

		if ($this->config->get('module_seo_url_generator_separator')) {
			$separator = $this->config->get('module_seo_url_generator_separator');
		} else {
			$separator = '-';
		}

		foreach ($this->request->post as $language_id => $name) {
			// Remove by regex non-alphanumeric letters except spaces. Method html_entity_decode remove quotes.
			$name = preg_replace("/[^A-Za-z0-9 ]/", '', html_entity_decode($name));

			// Remove dublicate spaces.
			$name = preg_replace('/\s+/', ' ', $name);

			// Replace spaces to separator.
			$name = str_replace(' ', $separator, $name);

			$name = trim($name, $separator);

			$name = mb_strtolower($name, 'UTF-8');

			$json['result'][$language_id] = $name;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
