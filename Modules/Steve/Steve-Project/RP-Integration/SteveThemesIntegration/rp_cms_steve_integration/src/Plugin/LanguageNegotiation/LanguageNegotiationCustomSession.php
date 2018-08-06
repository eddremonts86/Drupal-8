<?php

namespace Drupal\rp_cms_steve_integration\Plugin\LanguageNegotiation;

use Symfony\Component\HttpFoundation\Request;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationSession;

/**
 * Custom class for identifying language.
 *
 * @LanguageNegotiation(
 *   id = Drupal\rp_cms_steve_integration\Plugin\LanguageNegotiation\LanguageNegotiationCustomSession::METHOD_ID,
 *   weight = -99,
 *   name = @Translation("Extended Session"),
 *   description = @Translation("Language from a request/session parameter."),
 *   config_route_name = "language.negotiation_session"
 * )
 */
class LanguageNegotiationCustomSession extends LanguageNegotiationSession {

  /**
   * The language negotiation method id.
   */
  const METHOD_ID = 'language-extended-session';

  public function getLangcode(Request $request = NULL) {
  	$user_ip = $_SERVER["REMOTE_ADDR"];
    $config = $this->config->get('language.negotiation')->get('session');
    $param = $config['parameter'];
    $langcode = $request && $request->query->get($param) ? $request->query->get($param) : NULL;

    if (!$langcode && isset($_SESSION[$param])) {
      	$langcode = $_SESSION[$param];
    }else if(!$langcode && !isset($_SESSION[$param])){
    	$langcode = $this->languageManager->getDefaultLanguage()->getId();
    }

    $cookie = $request->cookies->get("Land");

    if($cookie){
    	if($cookie != $langcode){
    		$langcode = $cookie;
    	}
    }else{
    	$location = true ;//mb_strtolower(geoip_country_code_by_name($user_ip));

    	if($location){
    		if($location == 'ie'){
    			$langcode = 'ie';
    		}else{
    			$langcode = $this->languageManager->getDefaultLanguage()->getId();
    		}
    	}else{
    		$langcode = $this->languageManager->getDefaultLanguage()->getId();
    	}
    	setcookie('Land', $langcode, time() + 31556926);
    }
    return $langcode;
  }

}
