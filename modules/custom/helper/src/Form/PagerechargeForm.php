<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PagerechargeForm.
 */
class PagerechargeForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.pagerecharge',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pagerecharge_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('helper.pagerecharge');
    $form['le_lien_vers_la_page_recharger_v'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Le lien vers la page recharger votre compte'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $config->get('le_lien_vers_la_page_recharger_v'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('helper.pagerecharge')
      ->set('le_lien_vers_la_page_recharger_v', $form_state->getValue('le_lien_vers_la_page_recharger_v'))
      ->save();
  }

}
