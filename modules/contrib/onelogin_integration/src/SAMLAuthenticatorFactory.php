<?php

namespace Drupal\onelogin_integration;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\MissingDependencyException;
use Drupal\Core\Url;

/**
 * Class SamlAuthenticatorFactory.
 *
 * @package Drupal\onelogin_integration
 */
class SAMLAuthenticatorFactory implements SAMLAuthenticatorFactoryInterface {

  /**
   * The variable that holds an instance of ConfigFactoryInterface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * SamlAuthenticatorFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Reference to ConfigFactoryInterface.
   *
   * @throws \Drupal\Core\Extension\MissingDependencyException
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;

    // Check if OneLogin SAML library is installed.
    if (!class_exists('OneLogin_Saml2_Auth')) {
      throw new MissingDependencyException('The Onelogin Saml2 plugin is not correctly configured');
    }
  }

  /**
   * Settings for the OneLogin_Saml2_Auth library.
   *
   * Creates an instance of the OneLogin_Saml2_Auth library with default and,
   * if given, custom settings.
   *
   * @param array $settings
   *   Custom settings for the initialization of the OneLogin_Saml2_Auth
   *   library.
   *
   * @return \OneLogin_Saml2_Auth
   *   Returns a new instance of the OneLogin_Saml2_Auth library.
   */
  public function createFromSettings(array $settings = []) {
    $config = $this->configFactory->get('onelogin_integration.settings');

    $default_settings = [
      'strict' => $config->get('strict_mode'),
      'debug' => $config->get('debug'),

      'sp' => [
        'entityId' => $config->get('sp_entity_id'),
        'assertionConsumerService' => [
          'url' => Url::fromRoute('onelogin_integration.acs', [], ['absolute' => TRUE])->toString(),
        ],
        'singleLogoutService' => [
          'url' => Url::fromRoute('onelogin_integration.slo', [], ['absolute' => TRUE])->toString(),
        ],
        'NameIDFormat' => $config->get('nameid_format'),
        'x509cert' => $config->get('sp_x509cert'),
        'privateKey' => $config->get('sp_privatekey'),
      ],

      'idp' => [
        'entityId' => $config->get('entityid'),
        'singleSignOnService' => [
          'url' => $config->get('sso'),
        ],
        'singleLogoutService' => [
          'url' => $config->get('slo'),
        ],
        'x509cert' => $config->get('x509cert'),
      ],

      'security' => [
        'signMetadata' => FALSE,
        'nameIdEncrypted' => $config->get('nameid_encrypted'),
        'authnRequestsSigned' => $config->get('authn_request_signed'),
        'logoutRequestSigned' => $config->get('logout_request_signed'),
        'logoutResponseSigned' => $config->get('logout_response_signed'),
        'wantMessagesSigned' => $config->get('want_message_signed'),
        'wantAssertionsSigned' => $config->get('want_assertion_signed'),
        'wantAssertionsEncrypted' => $config->get('want_assertion_encrypted'),
        'relaxDestinationValidation' => TRUE,
      ],
    ];

    $settings = NestedArray::mergeDeep($default_settings, $settings);

    return new \OneLogin_Saml2_Auth($settings);
  }

}
