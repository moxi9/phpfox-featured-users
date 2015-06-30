<?php

/**
 *
 * For this app we return a callable function, which passes the current $App object
 */
return function(Core\App\Object $App, Twig_Environment $View) {

	// Check if user is logged in, if not don't load the event below
	$auth = new Core\Auth\User();
	if (!$auth->isLoggedIn()) {
		return false;
	}

	/**
	 * Attach an event to the loading of all blocks
	 */
	new Core\Event('lib_module_get_blocks', function(Phpfox_Module $object) use($View) {

		$db = new Core\Db();
		// $cache = new Core\Cache();

		$cond = [];
		$featured = $db->select('*')->from(':user_featured')->all();
		if ($featured) {
			$users = '';
			foreach ($featured as $user) {
				$users[] = (int) $user['user_id'];
			}

			$cond = ['user_id' => ['in' => implode(',', $users)]];
		}

		$users = new Api\User();
		$users->limit(6);
		$users->where($cond);
		$users->order('RAND()');

		$object->block('core.index-member', 1, $View->render('@PHPfox_FeaturedUsers/block.html', [
			'users' => $users->get()
		]));
	});
};