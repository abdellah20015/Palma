<?php

namespace Drupal\payment_gateway\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
/**
 * Class OkFailController.
 */
class OkFailController extends ControllerBase {

  /**
   * Ok.
   *
   * @return string
   *   Return Hello string.
   */
  public function OK() {
      $sorthtml = '';
      $user = \Drupal::currentUser();
      $user = User::load($user->id());
      $solde = $user->field_credit->value;
      $amount = isset( $_POST['amount'] ) ? $_POST['amount'] : 0;
      //$solde = $solde + $amount;
      //$user->set('field_credit', $solde);
      //$user->save();
      $sorthtml .=  '<div class="contenu_cmi"><h4 class="payClass">Paiement effectué</h4>'  . "<br />\r\n"; 
      $sorthtml .=  '<p class="credClass">La recharge de votre compte a été effectuée avec succès.<br />Vous venez de payer '.$amount.' MAD, votre nouveau solde est : '.$solde.' MAD</p></div>'  . " <br />\r\n"; 
    return [
      '#type' => 'markup',
      '#markup' => $sorthtml
    ];
  }

  /**
   * fail.
   *
   * @return string
   *   Return Hello string.
   */
  public function Fail() {
    $sorthtml = '';
    $sorthtml .= "<h2>Paiement échoué</h2>";
    $sorthtml .= "<p>Nous sommes navré de vous informer que l'opération n'est pas aboutie, merci de réessayer ultérieurement!</p><br /><br />";
    $sorthtml .= "<h3>Rappel de votre commande</h3>";
    $sorthtml .= "<p><b>Recharge :</b> ".$_POST['amount']." MAD</p><br /><br /><br /><br /><br /><br /><br /><br />";
    return [
      '#type' => 'markup',
      '#markup' => $sorthtml
    ];
  }

}
