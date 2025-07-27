<?php
/**
 * @file
 * Contains \Drupal\helper\Controller\AbonnementController.
 */

namespace Drupal\helper\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Url;
use \Drupal\Core\Routing\RouteMatchInterface; 
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbonnementController.
 */

class AbonnementController extends ControllerBase {
  /**
   * All abonnement from parent numero.
   *
   * @param $nid
   * @return mixed
   */

  public function abonnerNumero($node_id,$pr,$periode,$nbr_numeros) {
      $config = \Drupal::config('helper.pagerecharge');
      $config = \Drupal::config('helper.remise');
      $rms= $config->get('remise_par_defaut');
      $rmi= $rms /100;  
      $uid = \Drupal::currentUser()->id();
      $user = User::load( $uid );
      $roles = $user->getRoles();
      $solde=($user->field_credit) ? $user->field_credit->value: NULL;
      // decodage de valeurs
      $node_id = base64_decode($node_id);
      $pr = base64_decode($pr);
      $periode = base64_decode($periode);
      $nbr_numeros = base64_decode($nbr_numeros);
      $nodes = Node::load((int)$node_id);
      if ($nodes->get('field_journal_revue')) {
         $node_jr = $nodes->get('field_journal_revue')->entity;
         $namejr=$node_jr->get('title')->value;
         $type = $node_jr->get('field_type')->entity;
         $nametype= $type->get('name')->value;
      }
      // Reference node id du Numero
      $node = Node::load( $node_id );
      if ( $uid > 0 ) {
        if($this->checkmyNode($node_id) > 0) {
            drupal_set_message(t('Vous êtes déjà abonné à "' . $namejr . '"' ), 'warning');
            return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
        } else { 
          if (in_array('sous_compte', $roles)) {
            $uid_p =  $this->getparent($uid);
            $user_p = User::load($uid_p);
            $solde_p = $user_p->field_credit->value;
            if ($user->field_abonnement->value > 0) {
              if ($pr > $solde) {
                  drupal_set_message(t('Merci de recharger votre wallet'), 'warning');
                  return $this->redirect_url('/recharger_votre_compte');
                } else{
                  $this->abonnerVous($node_id,$pr,$periode,$namejr,$nbr_numeros);
                  $this->calculsouscompte('field_abonnement',$uid);
                  $this->calculmySolde($pr, $solde_p,$uid_p);
                }
            }
            return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
          }else{
              if ($pr > $solde) {
                      drupal_set_message(t('Merci de recharger votre wallet'), 'warning');
                      return $this->redirect_url('/recharger_votre_compte');
              }else{ 
                $this->abonnerVous($node_id,$pr,$periode,$namejr,$nbr_numeros);
                $this->calculmySolde($pr, $solde);
                drupal_set_message(t('Félicitation! Vous venez de vous abonner à "'.$namejr.'".'), 'status');
                return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
              }
          }     
        }
      } else {
        drupal_set_message(t('Veuillez vous connecter pour continuer'), 'warning');
        return $this->redirect_url('/user/login'); //?destination='.$node->toUrl()->toString()
      }
  }

  
/**
 *update for field pack in back office
* @return mixed
*/ 
public function addusersub($node_id) {
  $uid = \Drupal::currentUser()->id();
  $user = User::load( $uid );
  // modification du champ field_numero au niveau BO;
  $node = Node::load($node_id);   
  $node->field_numero[]=$node_id;
  $node->save();
}
   

public function abonnerVous($node_id,$pr,$periode,$namejr,$nbr_numeros) {
  $uid = \Drupal::currentUser()->id();
  $user = User::load( $uid );
  //recuperation des donnees des Journal/Revu
  $node = Node::load($node_id);
  if ( $node->get('field_journal_revue') ) {
    $node_jr = $node->get('field_journal_revue')->entity;
    $id_jr=$node_jr->id();
    $nb=1;
    $current_date = \Drupal::time()->getCurrentTime();
    $date_db=date('Y-m-d',$current_date);   
    $ladate=date('d M Y',$current_date); 
    $node = Node::create([
      'type' => 'abonner',
      'status' => 1,
      'title' => 'Abonnement '  . $user->name->value .' - '. $namejr . ' - '. $ladate,
      'field_periodicite' => [$periode],
      'field_prix' => [$pr],
      'field_journal_revue'=>[$id_jr],
      'field_user' => [$uid],
      'field_numeros_pack'=>[$nbr_numeros],
      'field_nbr_num'=>[$nb],
      'field_numero'=>[$node_id],
      'field_date'=>['value'=> $date_db],
      ]);
    $node->save();
  }
}

  /**
   * verifier l'existance dans db
   * @return mixed
   */
 
  public function checkmyNode($node_id) { 
    // $user = User::load(\Drupal::currentUser()->id());
    // $uid = $user->id();
    $uid = \Drupal::currentUser()->id();
    $query = \Drupal::entityQuery('node')
        ->condition('type','abonner')
        ->condition('field_numero',$node_id,'IN') 
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats; 
  } 

 
  
   /**
   * verifier l'existance dans db
   * @return mixed
   */

  public function checkTypenode($node_id) { 
    $uid = \Drupal::currentUser()->id();
     $query = \Drupal::entityQuery('node')
        ->condition('type','abonner')
        ->condition('field_thesubscribe',$node_id)
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats; 
  }

  /**
   * calculer solde
   * @return mixed
   */

  public function calculmySolde($prix,$solde) { 
    // $user = User::load(\Drupal::currentUser()->id());
    // $uid = $user->id();
    $uid = \Drupal::currentUser()->id();
    $user = User::load( $uid );
    $solde = $solde - $prix;
    $user->set('field_credit', $solde);
    $user->save();
  }


  

  private function redirect_url($destination){
    return new RedirectResponse($destination);
}


   
  /**
   * total abonner by user.
   *
   * @param $uid
   * @return mixed
   */
  public function gettotalabonner($uid) {
    $query = \Drupal::entityQuery('node')
        ->condition('type','abonner')
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }
  /**
   * total abonner by user.
   *
   * @param $uid
   * @return mixed 
   */
  public function getabonner($uid) {
    $query = \Drupal::entityQuery('node')
        ->condition('type','abonner')
        ->condition('created',strtotime("-1 days"), '>' )
        ->condition('created',strtotime("+1 days"), '<' )
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }

 


/**
   * total abonner by user.
   *
   * @param $uid
   * @return mixed
   */
  public function getabonnerparent() {
    // $user = User::load(\Drupal::currentUser()->id());
   $uid = \Drupal::currentUser()->id();
   $user = User::load( $uid );
   $sub_users = $user->get('field_sous_compte')->referencedEntities();
   foreach ($sub_users as $sub_user) {
      $uid = $sub_user->id();
      $resultats = $resultats + $this->getabonner($uid);
    }

     return $resultats;
  }

  /**
   * total getabonner by user.
   *
   * @param $uid
   * @return mixed
   */
  public function gettotalabonnerparent() {
    
    // $user = User::load(\Drupal::currentUser()->id());
   $uid = \Drupal::currentUser()->id();
   $user = User::load( $uid );
   $sub_users = $user->get('field_sous_compte')->referencedEntities();
   foreach ($sub_users as $sub_user) {
      $uid = $sub_user->id();
      $resultats = $resultats + $this->gettotalabonner($uid);
    }

     return $resultats;
  }
  /**
   * parent user.
   *
   * @param $uid
   * @return mixed
   */
  public function getparent($uid){
    $uid = \Drupal::currentUser()->id();
    $query = \Drupal::entityQuery('user');
    $query->condition('field_sous_compte', $uid);
    $parent_id = $query->execute();

    return $parent_id['1'];
  }

  /**
   * calculer sous compte
   * @return mixed
   */
  public function calculsouscompte($field,$uid) {
    $user = User::load($uid);
    if ($field == 'field_abonnement') {
      $compte = $user->field_abonnement->value - 1;
      $user->set('field_abonnement', $compte);
    }   
    $user->save();
  }
 
} 