<?php

namespace Drupal\rp_api;

/**
 * An interface for all RP API Model type plugins.
 */
interface RPAPIModelInterface {

  /**
   * Provide a description of the rpapimodel.
   *
   * @return string
   *   A string description of the rpapimodel.
   */
  public function description();

  /**
   * Provide a field mapping of the rpapimodel.
   *
   * @return array
   *   A array description of the rpapimodel.
   */
  public function mapping();

}
