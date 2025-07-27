<?php

namespace Drupal\payment_gateway\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Class CmiForm.
 */
class CmiForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cmi_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // obtenir l id de l utilisateur actuellement connecte
    $uid =\Drupal::currentUser()->id();
    //charge l objet utilisateur en spécifiant l'ID utilisateur à charger
    $user = User::load($uid);
    $current_date = \Drupal::time()->getCurrentTime();
    $date_=date('YmdHi',$current_date);
    
    $form['clientid'] = [
      '#type' => 'hidden',
      '#title' => $this->t('clientid'),
      '#weight' => '0',
      '#default_value'=> '600001332',
    ];
    $form['amount'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Veuillez saisir le montant de votre recharge en MAD'),
      '#weight' => '0',
      '#prefix' => '<div id="errmsg"></div>',
    ];
    $form['okurl'] = [
      '#type' => 'hidden',
      '#title' => $this->t('okUrl'),
      '#weight' => '0',
      '#default_value' => Url::fromRoute('payment_gateway.Ok', [], ['absolute' => TRUE])->toString(),
    ];
    $form['failurl'] = [
      '#type' => 'hidden',
      '#title' => $this->t('failUrl'),
      '#weight' => '0',
      '#default_value' => Url::fromRoute('payment_gateway.Fail', [], ['absolute' => TRUE])->toString(),
    ];
    $form['trantype'] = [
      '#type' => 'hidden',
      '#title' => $this->t('TranType'),
      '#weight' => '0',
      '#default_value' => 'PreAuth'
    ];
    $form['callbackurl'] = [
      '#type' => 'hidden',
      '#title' => $this->t('callbackUrl'),
      '#weight' => '0',
      '#default_value' => Url::fromRoute('payment_gateway.callback_controller_Callback', [], ['absolute' => TRUE])->toString(),
    ];
    $form['shopurl'] = [
      '#type' => 'hidden',
      '#title' => $this->t('shopurl'),
      '#weight' => '0',
      '#default_value' => 'https://www.palmadigitale.com/',
    ];
    $form['currency'] = [
      '#type' => 'hidden',
      '#title' => $this->t('currency'),
      '#weight' => '0',
      '#default_value' => "MAD"
    ];
    $form['rnd'] = [
      '#type' => 'hidden',
      '#title' => $this->t('rnd'),
      '#weight' => '0',
      '#default_value' => microtime()
    ];
    $form['storetype'] = [
      '#type' => 'hidden',
      '#title' => $this->t('storetype'),
      '#weight' => '0',
      '#default_value' => '3D_PAY_HOSTING'
    ];
    $form['hashAlgorithm'] = [
      '#type' => 'hidden',
      '#title' => $this->t('hashAlgorithm'),
      '#weight' => '0',
      '#default_value' => 'ver3'
    ];
    $form['lang'] = [
      '#type' => 'hidden',
      '#title' => $this->t('lang'),
      '#default_value' => 'fr',
      '#weight' => '0',
    ];
    $form['refreshtime'] = [
      '#type' => 'hidden',
      '#title' => $this->t('refreshtime'),
      '#default_value' => 5,
      '#weight' => '0',
    ];
    $form['BillToName'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToName'),
      '#weight' => '0',
      '#default_value' => $user->get('field_nom')->value
    ];
    $form['BillToCompany'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToCompany'),
      '#weight' => '0',
      '#default_value' => ''
    ];
    $form['BillToStreet1'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToStreet1'),
      '#weight' => '0',
      '#default_value' => $user->get('field_d_adresse_physique')->value
    ];
    $form['BillToCity'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToCity'),
      '#weight' => '0',
      '#default_value' => $user->get('field_ville')->value
    ];
    $form['BillToStateProv'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToStateProv'),
      '#weight' => '0',
      '#default_value' => ''
    ];
    $form['BillToPostalCode'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToPostalCode'),
      '#weight' => '0',
      '#default_value' => ''
    ];
    $form['BillToCountry'] = [
      '#type' => 'hidden',
      '#title' => $this->t('BillToCountry'),
      '#default_value' => 'Maroc',
      '#weight' => '0',
    ];
    $form['email'] = [
      '#type' => 'hidden',
      '#title' => $this->t('email'),
      '#default_value' => $user->getEmail(),
      '#weight' => '0',
    ];
    $form['tel'] = [
      '#type' => 'hidden',
      '#title' => $this->t('tel'),
      '#default_value' => $user->get('field_telephone')->value,
      '#weight' => '0',
    ];
    $form['encoding'] = [
      '#type' => 'hidden',
      '#title' => $this->t('encoding'),
      '#default_value' => 'UTF-8',
      '#weight' => '0',
    ];
    $form['oid'] = [
      '#type' => 'hidden',
      '#title' => $this->t('encoding'),
      '#default_value' => $user->id().'-'.$date_,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Recharger'),
    ];

    $form['#action'] = Url::fromRoute('payment_gateway.senddata_form', [], ['absolute' => TRUE])->toString();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    die();
    if (empty($form_state->getValue('amount')) || !is_numeric($form_state->getValue('amount'))) {
          $form_state->setErrorByName('amount', $this->t('Merci de saisir le montant de votre recharge'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
