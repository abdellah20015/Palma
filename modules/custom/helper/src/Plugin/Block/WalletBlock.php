<?php

namespace Drupal\helper\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\UncacheableDependencyTrait;
/**
 * Provides a 'WalletBlock' block.
 *
 * @Block(
 *  id = "wallet_block",
 *  admin_label = @Translation("Wallet block"),
 * )
 */
class WalletBlock extends BlockBase {
	use UncacheableDependencyTrait;
	/**
	* {@inheritdoc}
	*/
	public function build() {
	$user = User::load(\Drupal::currentUser()->id());

	$roles = $user->getRoles();
	$solde = array();
	if (in_array('sous_compte', $roles)) {
		$solde['sous_compte'] = TRUE;
		$solde['numero'] = explode('.', $user->field_nombre_achat_numero->value);
		$solde['abonnoment'] = explode('.', $user->field_abonnement->value);
	}else{
		$solde['credit'] = $user->field_credit->value;
	}
	return $solde;
	}
}