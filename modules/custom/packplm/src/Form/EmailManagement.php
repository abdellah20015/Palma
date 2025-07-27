<?php

namespace Drupal\packplm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EmailManagement extends ConfigFormBase {

  public function getFormId() {
    return 'packplm_admin_email_management';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'packplm.admin.management',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('packplm.admin.management');
    $form['#attached']['library'][] = 'helper/helper_css';
    $form['new_mail'] = [
      '#type' => 'details',
      '#title' => $this->t("Configuration de l'addresse Email du destinataire"),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $form['new_mail']['subject'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Sujet du mail'),
      '#required' => TRUE,
      '#default_value' => $config->get('new_mail_subject', ''),
    ];
    $form['new_mail']['content'] = [
      "#type" => 'textarea',
      '#title' => $this->t("Contenu de l'email"),
      '#required' => TRUE,
      '#default_value' => $config->get('new_mail_content', ''),
      '#description' => $this->t("Veuillez utiliser {name} pour afficher le nom de l'utilisateur qui a créé la demande."),
    ];

    $form['request_sent'] = [
      '#type' => 'details',
      '#title' => $this->t("Configuration de l'addresse Email de l'adaministrateur."),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $form['request_sent']['address'] = [
      "#type" => 'textfield',
      '#title' => $this->t("Adresse mail d'administrateur"),
      '#required' => TRUE,
      '#default_value' => $config->get('request_sent_address', ''),
    ];
    $form['request_sent']['subject'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Sujet du mail'),
      '#required' => TRUE,
      '#default_value' => $config->get('request_sent_subject', ''),
    ];
    $form['request_sent']['content'] = [
      "#type" => 'textarea',
      '#title' => $this->t("Contenu de l'email"),
      '#required' => TRUE,
      '#description' => $this->t("Veuillez utiliser {name} pour afficher le nom de l'utilisateur qui a créé la demande."),
      '#default_value' => $config->get('request_sent_content', ''),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('packplm.admin.management')
      ->set('new_mail_subject', $form_state->getValue('new_mail')['subject'])
      ->set('new_mail_content', $form_state->getValue('new_mail')['content'])
      ->set('request_sent_address', $form_state->getValue('request_sent')['address'])
      ->set('request_sent_subject', $form_state->getValue('request_sent')['subject'])
      ->set('request_sent_content', $form_state->getValue('request_sent')['content'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
