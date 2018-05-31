<?php
namespace Drupal\rp_style\Controller;

use Drupal\Core\Controller\ControllerBase;


class RPStyleController extends ControllerBase
{

    public function stylePage()
    {
        return array(
            '#theme' => 'styleguidepage',
        );
    }

}