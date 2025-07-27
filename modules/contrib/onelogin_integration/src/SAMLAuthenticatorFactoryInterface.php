<?php

namespace Drupal\onelogin_integration;

/**
 * Interface SamlAuthenticatorServiceInterface.
 *
 * @package Drupal\onelogin_integration
 */
interface SAMLAuthenticatorFactoryInterface {

  /**
   * Creates and/or returns in instance of the OneLogin_Saml2_Auth library.
   *
   * @param array $settings
   *   returns in instance of the OneLogin_Saml2_Auth library.
   */
  public function createFromSettings(array $settings);

}
