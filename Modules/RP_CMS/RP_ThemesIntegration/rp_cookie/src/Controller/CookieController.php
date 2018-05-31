<?php
namespace Drupal\rp_cookie\Controller;

/**
 * Class CookieController.
 */

Class CookieController{
	public function getCookieBlock($duration = 86400, $link = FALSE){
		$data = [
			'show' => TRUE,
			'link' => $link,
			'duration' => $duration
		];

		if(isset($_COOKIE["cookiepolicy"])){
			$data['show'] = FALSE;
		}
		
		return $data;
	}
}
