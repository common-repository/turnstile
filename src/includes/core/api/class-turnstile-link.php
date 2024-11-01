<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LinkRequest' ) ) :

	final class LinkRequest {

		static $url = "/api/turnstiles/";

		public static function list() {
			return new TurnstileAPIRequest(self::$url);
		}

		public static function get($id) {
			return new TurnstileAPIRequest(self::$url . $id);
		}

		public static function create($url, $embedded, $socials) {
			$data = array_merge(array(
				'url' => $url,
				'embedded' => $embedded,
			), $socials);
			return new TurnstileAPIRequest(self::$url, "POST", $data);
		}

		public static function update($id, $embedded, $socials) {
			$data = array_merge(array(
				'embedded' => $embedded,
			), $socials);
			return new TurnstileAPIRequest(self::$url . $id . '/', "PATCH", $data);
		}
	}

endif;
