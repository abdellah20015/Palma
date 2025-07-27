<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helper\Controller\PurchasedController;

/**
 * Class AffecterachatForm.
 */
class AffecterachatForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'affecterachat_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $nids = \Drupal::entityQuery('node')->condition('type','journal_revue')->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $sources[Null]=t('- Tout -');
    foreach ($nodes as $node) {
      $sources[$node->id()]=$node->get('title')->value;     
    }
     $options['']='- Selectionner -';
      $nids = \Drupal::entityQuery('node')->condition('type','numero')->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);

    foreach ($nodes as $node) {

        $options[$node->id()]=$node->get('title')->value;
     
        
    }
    $form['user'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('User'),
      '#target_type' => 'user',
      '#weight' => '0',
    ];
    $form['source'] = [
      '#type' => 'select',
      '#title' => $this->t('Source'),
      '#options' => $sources,
      '#default_value' => Null,
      '#weight' => '0',
    ];
    $form['source']['#ajax'] = [
      'callback' => '::numeros_dispo',
      'wrapper' => 'numeros_field',
      'event' => 'change',
      'method' => 'html',
      'effect' => 'fade',
    ];
    $form['numero'] = [
      '#type' => 'select',
      '#title' => $this->t('Numero'),
      '#options' => $options,
      '#default_value' => Null,
      '#attributes' => array('disabled'=>'disabled') ,
      '#prefix' => '<div id="numeros_field">',
      '#suffix' => '</div>',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function numeros_dispo(&$form, FormStateInterface $form_state, $form_id) {
    $helper = new PurchasedController();
      $options['']='- Selectionner -';
      $nids = \Drupal::entityQuery('node')->condition('type','numero')->condition('field_journal_revue',$form_state->getValue('source'))->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      if ($helper->checknode($form_state->getValue('user'),$node->id())== 0 && $this->outofsubscription($form_state->getValue('user'),$node->id()) == 0) {
        $options[$node->id()]=$node->get('title')->value;
      }
        
    }
      $form['numero']['#options'] = $options;
      $form['numero']['#attributes']['disabled'] = False;

    $form_state->setRebuild();
    return $form['numero'];
  }
  
  public function outofsubscription($uid,$node_id) {
    
    $query = \Drupal::entityQuery('node')
        ->condition('type','abonner')
        ->condition('field_numero',$node_id)
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
   $helper = new PurchasedController();
   if (!empty($form_state->getValue('user')) && !empty($form_state->getValue('numero'))) {
        if ($helper->checknode($form_state->getValue('user'),$form_state->getValue('numero')) > 0) {
          $form_state->setErrorByName('user', $this->t('Il exist deja '));
        } 
    }else{
      if (empty($form_state->getValue('user'))) {
          $form_state->setErrorByName('user', $this->t('Merci de bien renseigner le champ user'));
      }
      if (empty($form_state->getValue('numero'))) {
        $form_state->setErrorByName('numero', $this->t('Merci de bien renseigner le champ numero'));
      }
      
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $helper = new PurchasedController();
    $helper->purchased($form_state->getValue('numero'),$form_state->getValue('user'),'numero');
    drupal_set_message(t("L'action effectuer"), 'status');
  }

}