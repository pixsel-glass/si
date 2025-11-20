<?php
class ControllerToolGetLink extends Controller {
	public function index() {
		$url = $this->request->post['url'];

		if(strpos($_SERVER['SERVER_NAME'], 'catalog.') !== false) {
			$new_url = str_replace('pixsel.pl', 'catalog.pixsel.pl', $this->url->link('product/category', 'path=' . $url));
		} else if(strpos($_SERVER['SERVER_NAME'], 'price.') !== false) {
			$new_url = str_replace('pixsel.pl', 'price.pixsel.pl', $this->url->link('product/category', 'path=' . $url));
		}

		echo $new_url;
	}
}