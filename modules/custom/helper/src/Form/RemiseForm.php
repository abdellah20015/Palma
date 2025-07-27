<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RemiseForm.
 */
class RemiseForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.remise',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'remise_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('helper.remise');
    $form['remise_par_defaut'] = [
     // '#field_name' => 'field_remise', 
      '#type' => 'number',
      '#width' => '30%', 
      '#label' => 'My Field Remise', 
      '#title' => $this->t('Remise par defaut'),
      '#description' => $this->t("Le remise par défaut uniquement pour le Type Etudiant"),
      '#default_value' => $config->get('remise_par_defaut'),
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

    $this->config('helper.remise')
      ->set('remise_par_defaut', $form_state->getValue('remise_par_defaut'))
      ->save();
  }

}
