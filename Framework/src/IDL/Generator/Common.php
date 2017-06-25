<?php

namespace IDL\Generator;

/**
 * Class Common tool.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Common
{

	private function __construct() {

	}

	public static function sort($data) {
		$res = array();

		isset($data['name']) && $res['name'] = $data['name'];
		isset($data['type']) && $res['type'] = $data['type'];
		isset($data['require']) && $res['require'] = $data['require'];
		isset($data['description']) && $res['description'] = $data['description'];
		isset($data['validate']) && $res['validate'] = $data['validate'];
		isset($data['repeated']) && $res['repeated'] = $data['repeated'];
		isset($data['element']) && $res['element'] = $data['element'];

		return $res;
	}
}

// end of script