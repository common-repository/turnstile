<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TurnstileAPIRequest' ) ) :

final class TurnstileAPIRequest {

	public $url;
	public $method;
	public $data;
	public $headers;

	public function __construct($url, $method = "GET", $data = array(), $headers = array()) {
		$this->url = $url;
		$this->method = $method;
		$this->data = $data;
		$this->headers = array_merge( array(
			"Origin" => get_site_url(),
			"Content-Type" =>  "application/json",
		), $headers );
	}

	public function body() {
		if ( $this->headers["Content-Type"] === "application/json" ) {
			return wp_json_encode($this->data);
		} else {
			return $this->data;
		}
	}
}
endif;
