<?php
/**
 * @file
 * Contains \Drupal\packplm\Controller\MyMypackController.
 */

 
namespace Drupal\packplm\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Url;
use \Drupal\Core\Routing\RouteMatchInterface; 
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\Request;



/**
 * Class MypackController.
 */
class MypackController extends ControllerBase {

  /**
   * get path and function  pack_callback
   *
   * @param $nid
   * @return mixed
   */

  public function packcallback() { 
    // recuperation de la fonction  pack_callback
    $packs = $this->pack_callback($node_id); 
   // sauvegarde des variables dans un tableau
    return array(
      '#theme' => 'packs',
      '#packs' => $packs,
    );
    

  }


  /**
   * All Implementation for pack
   *
   * @param $nid
   * @return mixed
  */

  public function pack_callback($node_id) {
    // recuperation du id de l utilisateur encours
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    $node = Node::load($node_id);
    $var_id =  $user->field_pack->value;
    $type = $user->field_type->value;
    $mail = $user->getEmail();
    $title = $node->get('title')->value;
    // recupere la nom machine de titre
    $pack_id = $node->field_titre_machine->value;
    
      if ( $uid > 0 ) { 
        if ( $var_id ) {
          // Affiche le message du pack deja choisi
          drupal_set_message(t("Vous avez déjà choisi le Pack <span class='uppercase'>" .$var_id. "</span>"), 'warning');            // redirection  sur la meme page-abonnement
          return $this->redirect_url('/packs-abonnements'); 
        } else {
             	// si c etudiant et qui choisi un autre pack que le pack etudiant
				if( $type == "Etudiant" && $pack_id != "etudiant" ) {
  					drupal_set_message(t("Votre profil ne vous permet de choisir que le pack 'Etudiant'."), 'status');
  					return $this->redirect_url('/packs-abonnements');
  				}
  				// si c pas etudiant et qui choisi le pack etudiant
  				if( $type != "Etudiant" && $pack_id == "etudiant" ) {
  					drupal_set_message(t("Votre profil ne vous permet pas de choisir le pack 'Etudiant'!"), 'status');
  					return $this->redirect_url('/packs-abonnements'); 
  				}
  				
  				// modification au niveau du champ pack au BO
  				$this->upadpack($pack_id);
  				
  				if( $type != "Etudiant" ) {
  					// Envoi d email au demandeur de pack
            \Drupal::service('packplm.mail')->sendPackTwoRequestMail($title, $mail);
  					// Envoi d email de notification a l administrateur du site
  					\Drupal::service('packplm.mail')->sendStatusTwoNotifMail();
  					// affichage les message felicitation du pack
  					drupal_set_message(t("Nous vous remercions d'avoir choisi le Pack ".$title."! L'équipe Palma prendra contact avec vous le plutôt possible afin de valider cela."), 'status');
  				}        
  				// Envoi des emails configure au niveau de BO
  				if( $type == "Etudiant" ) {
  					// Envoi d email au demandeur de pack
            \Drupal::service('packplm.mail')->sendPackRequestMail($mail);
  					// Envoi d email de notification a l administrateur du site
  					\Drupal::service('packplm.mail')->sendStatusNotifMail();
  					// affichage les message felicitation du pack
  					drupal_set_message(t("Nous vous remercions d'avoir choisi le Pack ".$title."! L'équipe Palma prendra contact avec vous le plutôt possible afin de valider cela."), 'status');
				}

				// rediction a la page statistique de l utilisateur
				return $this->redirect_url('/user');            
         }
      } else {  
        // affiche le message pour ce connecte
        drupal_set_message(t(' Veuillez vous connecter afin de choisir le pack desiré!'), 'warning');
        // rediction au cas ou le l utilisateur n est pas connecte
        return $this->redirect_url('/user/login');
      }
    }
 
      /**
       *update for field pack in back office
      * @return mixed
      */ 
      public function upadpack($pack_id) {
        $user = User::load(\Drupal::currentUser()->id());
       // modification du champ pack au niveau BO;   
        $user->field_pack->value =$pack_id;
        $user->save();
      }
     
      /**
       * redirect_url
       * @return mixed
       */
      private function redirect_url($destination){
            return new RedirectResponse($destination);
      }
 
}