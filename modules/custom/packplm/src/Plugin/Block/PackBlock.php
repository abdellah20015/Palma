<?php

/**
 * Provides a 'Pack et abonnenment' Block
 *
 * @Block(
 *   id = "pksub_block",
 *   admin_label = @Translation("Bloc Pack et abonnenment"),
 * )
 */

namespace Drupal\packplm\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

class PackBlock extends BlockBase {
   
  /** 
   * {@inheritdoc}
   */
  public function build() { 
     // recuperation de la fct  packs
      $packs = $this->get_pack();  
      return array(
          '#theme' => 'packs',
          '#packs' => $packs,
        );

  }


  /**
   * Return list of pack
   *
   * @return array 
  */
  public function get_pack() { 
      $data = array();
      $query = \Drupal::entityQuery('node')
              ->condition('status', 1)
              ->condition('type', 'pack');
              $nids = $query->execute();
              if(!empty($nids)) {
                $data = node_load_multiple($nids, TRUE); 
                return $data;
              }
      return $data;
  }

}