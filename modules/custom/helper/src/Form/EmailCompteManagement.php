<?php

namespace Drupal\helper\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EmailCompteManagement extends ConfigFormBase {
  
  public function getFormId() {
    return 'helper_admin_emailcompteconfiguration';
  }

  

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.admin.emailcompteconfiguration',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('helper.admin.emailcompteconfiguration');
    $form['#attached']['library'][] = 'helper/helper_css';
    $form['my_mail'] = [
        '#type' => 'details',
        '#title' => $this->t("Configuration du Message pour le compte Utilisateur"),
        '#open' => TRUE,
        '#tree' => TRUE,
      ];
      $form['my_mail']['subject'] = [
        "#type" => 'textfield',
        '#title' => $this->t('Sujet du mail'),
        '#required' => TRUE,
        '#default_value' => $config->get('my_mail_subject', ''),
      ];
      $form['my_mail']['content'] = [
        "#type" => 'textarea',
        '#title' => $this->t("Contenu de l'email"),
        '#required' => TRUE,
        '#default_value' => $config->get('my_mail_content', ''),
       ];
       $form['request_sent'] = [
        '#type' => 'details',
        '#title' => $this->t("Configuration du Message pour le compte Adaministrateur "),
        '#open' => TRUE,
        '#tree' => TRUE,
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
    $this->config('helper.admin.emailcompteconfiguration')
       ->set('my_mail_subject', $form_state->getValue('my_mail')['subject'])
       ->set('my_mail_content', $form_state->getValue('my_mail')['content'])
      ->set('request_sent_subject', $form_state->getValue('request_sent')['subject'])
      ->set('request_sent_content', $form_state->getValue('request_sent')['content'])
       ->save();
    parent::submitForm($form, $form_state);
  }

}
