<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConditionForm.
 */
class ConditionForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.condition',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'condition_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('helper.condition');
    $form['cgu'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Conditions générales d'utilisation"),
      '#default_value' => $config->get('cgu'),
    ];
    $form['cookies_et_confidentialit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookies et confidentialités'),
      '#default_value' => $config->get('cookies_et_confidentialit'),
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

    $this->config('helper.condition')
      ->set('cgu', $form_state->getValue('cgu'))
      ->set('cookies_et_confidentialit', $form_state->getValue('cookies_et_confidentialit'))
      ->save();
  }

}
