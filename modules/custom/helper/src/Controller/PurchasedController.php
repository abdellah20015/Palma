<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\helper\Controller\NumeroController; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class PurchasedController.
 */
class PurchasedController extends ControllerBase {



  /**
   * Purchasednumero.
   *
   * @return string
   *   Return Hello string.
   */
  public function purchasednumero($node_id) {

      $config = \Drupal::config('helper.pagerecharge');
      $user = User::load(\Drupal::currentUser()->id());
      $uid = $user->id();
      $roles = $user->getRoles();
      $solde = $user->field_credit->value;
      $node = Node::load($node_id);
      $node_jr = $node->get('field_journal_revue')->entity;
      $type = $node_jr->get('field_type')->entity;    
      $prix = $node->get('field_prix')->value;
      if ($uid > 0 ) {
           
                if($this->checknode($uid,$node_id) > 0){
                  
                          drupal_set_message(t('Ce numéro est déjà acheté!'), 'warning');
                          return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
                }else{
                  if (in_array('sous_compte', $roles)) {
                        $uid_p =  $this->getparent($uid);
                        $user_p = User::load($uid_p);
                        $solde_p = $user_p->field_credit->value;
                        if ($user->field_nombre_achat_numero->value > 0) {
                          $this->purchased($node_id,$uid,'numero');
                          $this->calculsouscompte('field_nombre_achat_numero',$uid);
                          $this->calculsolde($prix, $solde_p,$uid_p);
                        }
                        
                        return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
                      }else{
                        if ($prix > $solde) {
                          
                          drupal_set_message(t('Merci de recharger votre wallet'), 'warning');
                          return $this->redirect_url('/recharger_votre_compte');
                        }else{
                             
                            $this->purchased($node_id,$uid,'numero');
                            $this->calculsolde($prix, $solde,$uid);
                            drupal_set_message(t('Votre achat a bien été effectué.'), 'status');
                            return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
                        }
                  }
                }
            
      }else{
        drupal_set_message(t('Veuillez vous connecter pour continuer'), 'warning');
        return $this->redirect_url('/user/login');//?destination='.$node->toUrl()->toString()
      }

  }

  /**
   * Purchasedarticle.
   *
   * @return string
   *   Return Hello string.
   */
  public function purchasedarticle($node_id) {
     $config = \Drupal::config('helper.pagerecharge');
      $numero = new NumeroController();
      $node_parent = $numero->getnumerobyarticle($node_id);
      $user = User::load(\Drupal::currentUser()->id());
      $uid = $user->id();
      $roles = $user->getRoles();
      $solde = $user->field_credit->value;
      $node = Node::load($node_id);
      $prix = $node->get('field_prix')->value;
      
      if ($uid > 0 ) {
          if($this->checknode($uid,$node_id) > 0){
              drupal_set_message(t('Cet article est déjà acheté!'), 'warning');
              return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
          }else{
            if ($this->checkarticle($uid,$node_id) > 0) {
              drupal_set_message(t('Cet article est déjà acheté!'), 'warning');
              return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
            }else{
              if (in_array('sous_compte', $roles)) {
                          
              }else{
                if ($prix > $solde) {
                            drupal_set_message(t('Merci de recharger votre wallet'), 'warning');
                            return $this->redirect_url('/recharger_votre_compte');
                }else{

                    $this->purchased($node_id,$uid,'article');
                    $this->calculsolde($prix, $solde,$uid);
                    return $this->redirect_url($node->toUrl()->setAbsolute()->toString());
                }
              }
            }
          }
      }else{
        drupal_set_message(t('Veuillez vous connecter pour continuer'), 'warning');
        return $this->redirect_url('/user/login');//?destination='.$node_parent->toUrl()->toString()
      }
      

  }
  /**
   * purchased
   * @return mixed
   */
  public function purchased($node_id,$uid,$type_p) {
    if ($type_p == 'numero') {
        $node = Node::load($node_id);
        $nodes_pages = $node->get('field_pages')->referencedEntities();
        $prix = $node->get('field_prix')->value;
        $node_jr = $node->get('field_journal_revue')->entity;
        $type = $node_jr->get('field_type')->entity;
        if ($type) {
          $type = $type->get('name')->value;
        }else{
          $type = 'Journal';
        }
        foreach ($nodes_pages as $item_page) {
            $articles = $item_page->get('field_les_articles')->referencedEntities();
            foreach ($articles as $article) {
              if($this->checknode($uid,$article->id()) == 0){
                 $this->accessarticle($article->id(),$uid);
               }
            }
        }
        $this->archive($node_id,'numero',$prix,$node_id,$type,$uid);
    }else{  
      $numero = new NumeroController();
      $node_parent = $numero->getnumerobyarticle($node_id);
      $node = Node::load($node_id);
      $prix = $node->get('field_prix')->value;
      $this->accessarticle($node_id,$uid);
      //archiver l'achat du numero
      $this->archive($node_id,'article',$prix,$node_parent->id(),'Article',$uid);
    }
  }

  /**
   * archive achat
   * @return mixed
   */
  public function archive($node_id,$type,$prix,$numero,$origine,$uid) {
    $current_date = \Drupal::time()->getCurrentTime();
    $user = User::load(\Drupal::currentUser()->id());
    $uid = $user->id();
    $node = Node::create([
      'type' => 'acheter',
      'status' => 1,
      'title' => 'achat '. $user->name->value .' / '.$current_date,
      'field_type_contenu' => $type,
      'field_contenu' => [$node_id],
      'field_numero' => [$numero],
      'field_prix' => [$prix],
      'field_user' => [$uid],
      'field_origine_dachat' => $origine,
    ]);
    $node->save();

  }

  /**
   * access article
   * @return mixed
   */
  public function accessarticle($node_id,$uid) {
    $node = Node::load($node_id);
    $node->field_user_scribe[] = $uid;
    $node->save();
  }

  /**
   * verifier l'existance dans db
   * @return mixed
   */
  public function checkarticle($uid,$node_id) {
    
    $query = \Drupal::entityQuery('node')
        ->condition('type','article_numero')
        ->condition('nid',$node_id)
        ->condition('field_user_scribe',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }
  /**
   * verifier l'existance dans db
   * @return mixed
   */
  public function checknode($uid,$node_id) {
    
    $query = \Drupal::entityQuery('node')
        ->condition('type','acheter')
        ->condition('field_contenu',$node_id)
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }

  /**
   * calculer solde
   * @return mixed
   */
  public function calculsolde($prix,$solde,$uid) {
    $user = User::load($uid);
    $solde = $solde - $prix;
    $user->set('field_credit', $solde);
    $user->save();
  }


  private function redirect_url($destination){
      return new RedirectResponse($destination);
  }

  /**
   * total achat by user.
   *
   * @param $uid
   * @return mixed
   */
  public function gettotalachat($uid) {

    $query = \Drupal::entityQuery('node')
        ->condition('type','acheter')
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }
  /**
   * total achat by user.
   *
   * @param $uid
   * @return mixed
   */
  public function getachat($uid) {

    $query = \Drupal::entityQuery('node')
        ->condition('type','acheter')
        ->condition('created',strtotime("-1 days"), '>' )
        ->condition('created',strtotime("+1 days"), '<' )
        ->condition('field_user',$uid);
     $resultats = $query->count()->execute();
     return $resultats;
  }
  /**
   * total achat by user.
   *
   * @param $uid
   * @return mixed
   */
  public function getachatparent() {

    $user = User::load(\Drupal::currentUser()->id());
   
   $sub_users = $user->get('field_sous_compte')->referencedEntities();
   foreach ($sub_users as $sub_user) {
      $uid = $sub_user->id();
      $resultats = $resultats + $this->getachat($uid);
    }

     return $resultats;
  }

  /**
   * total achat by user.
   *
   * @param $uid
   * @return mixed
   */
  public function gettotalachatparent() {
    
    $user = User::load(\Drupal::currentUser()->id());
   
   $sub_users = $user->get('field_sous_compte')->referencedEntities();
   foreach ($sub_users as $sub_user) {
      $uid = $sub_user->id();
      $resultats = $resultats + $this->gettotalachat($uid);
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
    $query = \Drupal::entityQuery('user');
    $query->condition('field_sous_compte', $uid);
    $parent_id = $query->execute();
    foreach ($parent_id as $key => $value) {
        return $key;
    }
  }

  /**
   * calculer sous compte
   * @return mixed
   */
  public function calculsouscompte($field,$uid) {
    $user = User::load($uid);
    if ($field == 'field_nombre_achat_numero') {
      $compte = $user->field_nombre_achat_numero->value - 1;
      $user->set('field_nombre_achat_numero', $compte);
    }   
    $user->save();
  }
}
