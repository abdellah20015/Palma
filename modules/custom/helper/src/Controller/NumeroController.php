<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
/**
 * Class NumeroController.
 */
class NumeroController extends ControllerBase {

  /**
   * Allarticles from parent numero.
   *
   * @param $nid
   * @return mixed
   */
  public function allarticles($nid) {

    $node =$this->getnumerobyarticle($nid);
    $nodes_pages = $node->get('field_pages')->referencedEntities();
    $uid = \Drupal::currentUser()->id();
    $i=0;
    $articles_list=array();
    $price = 0;
   foreach ($nodes_pages as $item_page) {
      $articles = $item_page->get('field_les_articles')->referencedEntities();
      foreach ($articles as $article) {
        $users_in_article = $article->get('field_user_scribe')->referencedEntities();

          $user_in_artcle = array();
          foreach($users_in_article as $team){
             $user_in_artcle[] = $team->id();
          }

          if (in_array($uid, $user_in_artcle)) {
                    $articles_list[$i]['url']= $article->toUrl()->setAbsolute()->toString();
                    $a= $i+1;
                    $articles_list[$i]['title']= 'Article '.$a ;
                    $price = $price + $article->get('field_prix')->value ;
                    if ($article->id()==$nid) {
                        $articles_list[$i]['current']= 'active';
                    }else{
                        $articles_list[$i]['current']= '';
                    }
                    $i++;
                    
          }

    }
    
   }

   $articles_list['total_price'] = $price;
   $articles_list['total_article'] = $i;
   return $articles_list;
  }

  /**
   * get numero by articles.
   *
   * @param $nid
   * @return mixed
   */
  public function getnumerobyarticle($nid) {
    $querypage = \Drupal::entityQuery('node')
        ->condition('type','page_numero')
        ->condition('status', 1)
        ->condition('field_les_articles',$nid);
      $nidpage = $querypage->execute();
      $querynumero = \Drupal::entityQuery('node')
        ->condition('type','numero')
        ->condition('status', 1)
        ->condition('field_pages',reset($nidpage));
      $nidnumero = $querynumero->execute();

    $node =  Node::load(reset($nidnumero));
   return $node;
  }
}
