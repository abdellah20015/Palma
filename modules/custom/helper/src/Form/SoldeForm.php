<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SoldeForm.
 */
class SoldeForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.solde',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'solde_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('helper.solde');
    $form['solde_par_defaut'] = [
      '#type' => 'number',
      '#title' => $this->t('Solde par defaut'),
      '#description' => $this->t('Le solde par défaut après la création d&#039;un compte'),
      '#default_value' => $config->get('solde_par_defaut'),
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

    $this->config('helper.solde')
      ->set('solde_par_defaut', $form_state->getValue('solde_par_defaut'))
      ->save();
  }

}
