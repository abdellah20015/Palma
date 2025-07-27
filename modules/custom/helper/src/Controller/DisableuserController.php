<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Routing\RouteMatchInterface;
use \Drupal\Core\Url;

/**
 * Class DisableuserController.
 */
class DisableuserController extends ControllerBase {

  /**
   * Disableuser.
   *
   * @return string
   *   Return Hello string.
   */
  public function disableuser() {
    $user = User::load(\Drupal::currentUser()->id());
    $uid = $user->id();
    if ($uid > 0 ) {
            $user->status = 0;
            $user->save();
            // \Drupal::service('helper.palmamail')->sendStatusRequestMail();        
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Votre compte a été désactivé')
    ];
  }

  /**
   * Enableuser.
   *
   * @return string
   *   Return Hello string.
   */
  public function enableuser(RouteMatchInterface $route_match) {

    $user = $route_match->getParameter('uid');
 

    if ($user->id() > 0 ) {
            $user->status = 1;
            $user->save(); 
            $text = '';
            $text .= '<h4 class="payClass">Activation de votre compte</h4><p>Nous vous informons que votre compte a bien été activé.</p>';
            $text .= 'Veuillez vous connecter en cliquant <a href="'.Url::fromRoute('user.login', [], ['absolute' => TRUE])->toString().'">ici</a> !';

            return [
              '#type' => 'markup',
              '#markup' => $text
            ];    
    }
   
    return [
      '#type' => 'markup',
      '#markup' => $this->t('error')
    ];  
  }

}
