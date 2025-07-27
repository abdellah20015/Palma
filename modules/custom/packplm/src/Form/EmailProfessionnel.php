<?php

namespace Drupal\packplm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EmailProfessionnel extends ConfigFormBase {

  public function getFormId() {
    return 'packplm_admin_email_professionnel';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'packplm.admin.professionnel',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('packplm.admin.professionnel');
    $form['#attached']['library'][] = 'helper/helper_css';
    $form['two_mail'] = [
      '#type' => 'details',
      '#title' => $this->t("Configuration de l'addresse Email du destinataire"),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $form['two_mail']['subject'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Sujet du mail'),
      '#required' => TRUE,
      '#default_value' => $config->get('two_mail_subject', ''),
    ];
    $form['two_mail']['content'] = [
      "#type" => 'textarea',
      '#title' => $this->t("Contenu de l'email"),
      '#required' => TRUE,
      '#default_value' => $config->get('two_mail_content', ''),
      '#description' => $this->t("Veuillez utiliser {name}, {pack} pour afficher successivement le nom de l'utilisateur qui a créé la demande et le pack choisi."),
    ];

    $form['req_sent'] = [
      '#type' => 'details',
      '#title' => $this->t("Configuration de l'addresse Email de l'adaministrateur."),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $form['req_sent']['address'] = [
      "#type" => 'textfield',
      '#title' => $this->t("Adresse mail d'administrateur"),
      '#required' => TRUE,
      '#default_value' => $config->get('req_sent_address', ''),
    ];    
    $form['req_sent']['subject'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Sujet du mail'),
      '#required' => TRUE,
      '#default_value' => $config->get('req_sent_subject', ''),
    ];
    $form['req_sent']['content'] = [
      "#type" => 'textarea',
      '#title' => $this->t("Contenu de l'email"),
      '#required' => TRUE,
      '#description' => $this->t("Veuillez utiliser {name} pour afficher le nom de l'utilisateur qui a créé la demande."),
      '#default_value' => $config->get('req_sent_content', ''),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('packplm.admin.professionnel')
      ->set('two_mail_subject', $form_state->getValue('two_mail')['subject'])
      ->set('two_mail_content', $form_state->getValue('two_mail')['content'])
      ->set('req_sent_address', $form_state->getValue('req_sent')['address'])
      ->set('req_sent_subject', $form_state->getValue('req_sent')['subject'])
      ->set('req_sent_content', $form_state->getValue('req_sent')['content'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
