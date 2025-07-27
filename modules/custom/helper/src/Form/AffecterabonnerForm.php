<?php

namespace Drupal\helper\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helper\Controller\AbonnementController;
use Drupal\paragraphs\Entity\Paragraph;



/**
 * Class AffecterabonnerForm.
 */
class AffecterabonnerForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'affecterabonner_form';
  }

  
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'helper.abonnement',
    ];
  }


  /**
   * Construction de form
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['user'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('User'),
      '#target_type' => 'user',
      '#weight' => '0',
    ];

    $form['user']['#ajax'] = [
      'callback' => '::sources_dispo',
      'wrapper' => 'source_field',
      'event' => 'autocompleteclose change',
      'method' => 'html',
      'effect' => 'fade',
     ];

    $form['sources'] = [
      '#type' => 'select',
      '#title' => $this->t('Sources'),
      '#options' => $sources,
      '#default_value' => Null,
      '#prefix' => '<div id="source_field">',
      '#suffix' => '</div>',
    ];

    $form['sources']['#ajax'] = [
      'callback' => '::packs_dispo',
      'wrapper' => 'pack_field',
      'event' => 'change',
      'method' => 'html',
      'effect' => 'fade',
     ];

    $form['packs'] = [
      '#type' => 'select',
      '#title' => $this->t('Packs abonnement'),
      '#options' => $packs,
      '#default_value' => Null,
      '#prefix' => '<div id="pack_field">',
      '#suffix' => '</div>',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  } 
  
  /*
    /* Chargement des journaux auquels je suis pas abonne 
  */
  public function sources_dispo(&$form, FormStateInterface $form_state, $form_id) {
    $sources = [];
    $sources['']=t('- Selectionnez -');
    $nids   = \Drupal::entityQuery('node')->condition('type','abonner')
              ->condition('field_user',$form_state->getValue('user'),'=')
              ->execute();
    $nodes  = \Drupal\node\Entity\Node::loadMultiple($nids);
    foreach ($nodes as $data) {
      if( $data->get('field_journal_revue') ) {
        $idnumbr[] = $data->get('field_journal_revue')->entity->id();
      }
    }
    $_nids  = \Drupal::entityQuery('node')->condition('type','journal_revue')->execute();
    if ( $idnumbr ) $nids_sans = array_diff($_nids, $idnumbr);
    else $nids_sans = $_nids;
    $_nodes = \Drupal\node\Entity\Node::loadMultiple($nids_sans);
    foreach ($_nodes as $_node) {
      $sources[$_node->id()]= $_node->get('title')->value;   
    }
    $form['sources']['#options'] = $sources;
    $form_state->setRebuild();
    return $form['sources'];
  }

  /**
   * Chargement de packs d un journal
  */
  public function packs_dispo(&$form, FormStateInterface $form_state, $form_id) {
    $packs['']=t('- Selectionnez -');
    $node = \Drupal::entityTypeManager()->getStorage('node')->load( $form_state->getValue('sources') );
        // Obtenir des données du packsabonnement.
    if ($pgitems = $node->get('field_lesabonnements')->getValue()) {
          // Obtenir de stockage.
          $pstrg = \Drupal::entityTypeManager()->getStorage('paragraph');
          // recupere le ID du champ paragraph.
          $ids = array_column($pgitems, 'target_id');
          // Charger tous object du paragraphe.
          $paragraphs = $pstrg->loadMultiple($ids);
          // recuperation les champ avec sa cle
          foreach ($paragraphs as $key => $paragraph) {
            $packs[$key] = $paragraph->get('field_titre')->value;
        }
    }
     
    $form['packs']['#options'] = $packs;
    $form_state->setRebuild();
    return $form['packs'];
  } 

  /**
   * Validation de form
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    
    if (empty($form_state->getValue('user'))) {
        $form_state->setErrorByName('user', $this->t('Merci de bien renseigner le champ user'));
      }
    
    if (empty($form_state->getValue('sources'))) {
        $form_state->setErrorByName('sources ', $this->t('Merci de bien renseigner le champ sources'));
      }
    
    if (empty($form_state->getValue('packs'))) {
        $form_state->setErrorByName('packs', $this->t('Merci de bien renseigner le champ Packs abonnement'));
    }

  }


  /**
   * Envoie de form
   */   
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $current_date = \Drupal::time()->getCurrentTime();
    $date=date('Y-m-d',$current_date);   
    $idjr=$form_state->getValue('sources');
    // recupere le uid
    $uid=$form_state->getValue('user');
    // application de la remise pour le cas etudiant
    $config = \Drupal::config('helper.remise');
    $rms= $config->get('remise_par_defaut');
    // recuperation de lid pack
    $ids=$form_state->getValue('packs');
    // chargement du lid packs
    $pgph = Paragraph::load( $ids );
    $prix= ($pgph->field_prix) ? $pgph->field_prix->value: NULL;
    $rmi= $rms /100;
     // chargement du uid
    $user = \Drupal\user\Entity\User::load( $uid );
    $roles = $user->getRoles();
    if (in_array('etudiant', $roles)) {
       $pr = $prix - ($prix * $rmi);
       } else{
       $pr= $prix;
    }

    $nbr_num= ($pgph->field_nbr_numpack) ? $pgph->field_nbr_numpack->value: NULL;
    $priod_id = '';
    if ( $period = $pgph->get('field_periodicite')->entity) {
       if ( $period ) {
         $prdid= $period->id();
       }
     }
   
    $nb = 1;
    $nids = \Drupal::entityQuery('node')->condition('type','numero')
    ->condition('field_journal_revue',$idjr,'=')
    ->sort('field_date','desc')
    ->range( 0 , 1 )
    ->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    foreach ($nodes as $node) {
          $node_id=$node->id();
          if ($node->get('field_journal_revue')) {
            $nodejr = $node->get('field_journal_revue')->entity;
            $namejr= $nodejr->get('title')->value;
          }
    }

   // traitement dabonnement 
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'abonner',
      'status' => 1,
      'title' => 'Abonnement '  . $user->name->value .' - '. $namejr . ' - '. $date,
      'field_periodicite' => [$prdid],
      'field_prix' => [$pr],
      'field_journal_revue'=>[$idjr],
      'field_user' => [$uid],
      'field_numeros_pack'=>[$nbr_num],
      'field_nbr_num'=>[$nb],
      'field_numero'=>[$node_id],
      'field_date'=>['value'=> $date],
      ]);
    $node->save();
    drupal_set_message(t("L'action étè bien effectuer"), 'status');
 
  }


}