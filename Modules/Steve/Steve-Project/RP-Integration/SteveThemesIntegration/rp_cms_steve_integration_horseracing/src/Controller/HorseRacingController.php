<?php
namespace Drupal\rp_cms_steve_integration_horseracing\Controller;

use Drupal\rp_client_base\Controller\SteveFrontendControler;

/**
 * Class HorseRacingController.
 */

Class HorseRacingController extends SteveFrontendControler{

	public function horseHomePage()
	{
		return array(
			'#theme' => 'horsehomepage',
		);
	}
	public function horseWatchLivePage()
	{
		return array(
			'#theme' => 'horsewatchlivepage',
		);
	}
	public function horseRaceCoursesPage()
	{
		return array(
			'#theme' => 'horseracecoursespage',
		);
	}
	public function horseRaceCoursePage()
	{
		return array(
			'#theme' => 'horseracecoursepage',
		);
	}
	public function horseReviewsPage()
	{
		return array(
			'#theme' => 'horsereviewspage',
		);
	}
	public function horseReviewPage()
	{
		return array(
			'#theme' => 'horsereviewpage',
		);
	}
	public function horseBlogPage()
	{
		return array(
			'#theme' => 'horseblogpage',
		);
	}
}
