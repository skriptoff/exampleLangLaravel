<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Lang for save and get strings from db and using variables which will be replaced by value
 *
 * @package App
 * @author Artem Chub
 */
class Lang extends Model
{
	private static $instance = null;
	private static $lang = null;

	private function __clone() {}

	/**
	 * Private constructor for pattern singleton.
	 * Connection to db once.
	 *
	 * @return void
	 */
	private function __construct()
	{
		 foreach (DB::table('langs')->select('key', 'value')->get() as $lang) {
			 self::$lang[$lang->key] = $lang->value;
		 }
	}

	/**
	 * Initial method to get instance
	 *
	 * @return self instance
	 */
	public static function init()
	{
		if (null === self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get string by key from db. Add default string if not exists.
	 *
	 * @param string $key for search string
	 * @param null $default will add string if key not exists in db
	 * @return bool|string
	 */
	public static function get(string $key, $default = null)
	{
		self::init();
		if (!empty(self::$lang[$key])) {
			return self::$lang[$key];
		}
		return ($default && self::set($key, $default)) ? $default : false;
	}

	/**
	 * Add new string by key
	 *
	 * @param string $key
	 * @param string $value
	 * @return bool
	 */
	public static function set(string $key, string $value)
	{
		self::init();
		DB::table('langs')
			->updateOrInsert(
				['key' => $key],
				['value' => $value]
			);
		self::$lang[$key] = $value;
		return true;
	}

	/**
	 * Get all existing strings from db
	 *
	 * @return object
	 */
	public static function getAll()
	{
		$instance = self::init();
		return (object) $instance::$lang;
	}

	/**
	 * Assign variables to string and replace to value. Example {name}, {date} etc
	 *
	 * @param string $key of string to get
	 * @param array $vars array of variables for replace, example ['name' => 'John', 'date' => date('Y-m-d')].
	 * @param null $default will add string if key not exists in db
	 * @return string result after replacing
	 */
	public static function assign(string $key, array $vars, $default = null)
	{
		$value = self::get($key, $default);
		foreach ($vars as $k => $v) {
			$value = preg_replace("/\{$k\}/iu", $v, $value);
		}
		return $value;
	}
}
