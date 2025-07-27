<?php

namespace Drupal\helper;
use Drupal\user\Entity\User;
/**
 * Class HelperService.
 */
class HelperService {
	protected $current_user;
  /**
   * Constructs a new HelperService object.
   */
  public function __construct(\Drupal::currentUser()->id() $current_user_id) {
  	$this->current_user = User::load($current_user_id);
  }

   /**
   * calculer solde
   * @return mixed
   */
  private function regenerateSolde($numero) {
  	$prix = $numero->get('field_prix')->value;
  	$solde = $this->current_user->field_credit->value;
    $solde = $solde - $prix;
    $this->current_user->set('field_credit', $solde);
    $this->current_user->save();
  }
}
