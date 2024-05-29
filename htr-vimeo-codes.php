<?php
/**
	* Plugin Name: Hit The Road — Codes Viméo
	*	Plugin URI: https://github.com/EmmanuelBeziat/htr-vimeo-codes
	*	Description: Wordpress plugin for Hit the Road website, that allow the inclusion of multiple promo codes in a custom table.
	*	Version: 1.0.0
	*	Author: Emmanuel Béziat
	*	Author URI: https://www.emmanuelbeziat.com
	*	GitHub Plugin URI: https://github.com/EmmanuelBeziat/htr-vimeo-codes
	*	License: MIT License
	*	License URI: http://opensource.org/licenses/MIT
	*
	* @author Emmanuel Béziat
	* @copyright Emmanuel Béziat
	* @link https://github.com/EmmanuelBeziat/htr-vimeo-codes
	* @package Hit The Road — Codes viméo
	* @license http://opensource.org/licenses/MIT
	*/

if (!defined('ABSPATH')) die();

require_once(plugin_dir_path(__FILE__) . 'classes/database.php');
require_once(plugin_dir_path(__FILE__) . 'classes/code.php');
require_once(plugin_dir_path(__FILE__) . 'classes/form-handler.php');

use HTRVC\Database;
use HTRVC\Code;
use HTRVC\FormHandler;

if (!class_exists('HTRVimeoCode')) {
	class HTRVimeoCode {
		public $version = '1.0.0';
		private $code;
		private $formHandler;

		public function __construct () {
			$this->defines();
			$this->code = new Code($GLOBALS['wpdb']);
			$this->formHandler = new FormHandler();
			add_action('admin_menu', [$this, 'setupAdminMenu']);
			add_action('admin_enqueue_scripts', [$this, 'addAdminAssets']);
		}

		private function defines () {
			define('HTRVC_PATH', plugin_dir_path(__FILE__));
			define('HTRVC_VERSION', $this->version);
		}

		public function setupAdminMenu () {
			add_submenu_page(
				'tools.php',
				'Import de codes Viméo',
				'Import de codes Viméo',
				'manage_options',
				'htrvc-vimeo-import',
				[$this, 'viewAdminImport']
			);
		}

		public function addAdminAssets () {
			wp_enqueue_media();
			wp_enqueue_script('htrcv-admin-js', plugins_url('htr-vimeo-codes/assets/js/admin.js', __DIR__), [], HTRVC_VERSION, true);
			wp_enqueue_style('htrcv-admin-styles', plugins_url('htr-vimeo-codes/assets/css/admin.css', __DIR__), [], HTRVC_VERSION);
		}

		public function viewAdminImport () {
			try {
				$this->formHandler->handleFormSubmission($this->code, 'code');
				$entries = $this->code->list();
				include_once(HTRVC_PATH . 'views/admin/import.php');
			}
			catch (Exception $e) {
				error_log('Caught exception: ' . $e->getMessage(), 0);
				throw $e;
			}
		}
	}

	global $HTRVimeoCode;
	$HTRVimeoCode = new HTRVimeoCode();

	// Database
	$database = new Database($GLOBALS['wpdb']);
	$database->createTableCodesList();
}

