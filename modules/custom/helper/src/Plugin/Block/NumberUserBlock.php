<?php

namespace Drupal\helper\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'NumberUserBlock' block.
 *
 * @Block(
 *  id = "number_user_block",
 *  admin_label = @Translation("Number User Block"),
 * )
 */
class NumberUserBlock extends BlockBase {
	/**
	* {@inheritdoc}
	*/
	public function build() {
		$count_users_tot = db_query('SELECT COUNT(uid) FROM {users}')->fetchField();
		$count_users_tot --;
		echo '<h2>Nombre total des utilsateurs est : ' . $count_users_tot . '</h2>';
	}
}