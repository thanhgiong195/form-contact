<?php

/*
Plugin Name: Manage Contact Form
Plugin URI: https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
Description: Manage Contact Form
Author: Thanh Giong
Version: 1.0
*/

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

function add_head_events()
{
	$nonce = wp_create_nonce('jsforwp_events');

	wp_enqueue_script('js_script', plugins_url('js/script.js', __FILE__));

	wp_localize_script(
		'js_script',
		'jsforwp_event_globals',
		[
			'ajax_url'    => admin_url('admin-ajax.php'),
			'nonce'       => $nonce
		]
	);
}

add_action('admin_init', 'add_head_events');

function update_policy()
{
	global $wpdb;

	$query = "UPDATE wp8_contact_setting ";
	$query .= 'SET policy = "' . $_POST["policy"] . '", inquiry = "' . $_POST["inquiry"] . '", email_content = "' . $_POST["email_content"] . '", thanks_content = "' . $_POST["thanks_content"] . '"';
	$query .= 'WHERE id = 1';

	$wpdb->get_results($query, 'ARRAY_A');
	$response['status'] = 'success';
	$response['query'] = $query;

	check_ajax_referer('jsforwp_events');
	$response = json_encode($response);
	echo $response;

	die();
}
add_action('wp_ajax_update_policy', 'update_policy');

class Care_List extends WP_List_Table
{

	/** Class constructor */
	public function __construct()
	{

		parent::__construct([
			'singular' => __('Customer', 'sp'), //singular name of the listed records
			'plural'   => __('Customers', 'sp'), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		]);
	}


	/**
	 * Retrieve s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_dataform($per_page = 10, $page_number = 1)
	{

		global $wpdb;

		$sql = "SELECT * FROM wp8_contact_data";
		$sql .= " ORDER BY id DESC";
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

		$result = $wpdb->get_results($sql, 'ARRAY_A');

		return $result;
	}


	/**
	 * Delete a  record.
	 *
	 * @param int $id  ID
	 */
	public static function delete_data_form($id)
	{

		global $wpdb;

		$wpdb->delete(
			'wp8_contact_data',
			['ID' => $id],
			['%d']
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count()
	{
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM wp8_contact_data";

		return $wpdb->get_var($sql);
	}


	/** Text displayed when no  data is available */
	public function no_items()
	{
		_e('No data.', 'sp');
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name)
	{
		return $item[$column_name];
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	// function column_name( $item ) {

	// 	$delete_nonce = wp_create_nonce( 'sp_delete_data_form' );

	// 	$title = '<strong>' . $item['username'] . '</strong>';

	// 	$actions = [
	// 		'delete' => sprintf( '<a href="?page=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
	// 	];

	// 	return $title . $this->row_actions( $actions );
	// }


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns()
	{
		$columns = [
			'cb'				=> '<input type="checkbox" />',
			'name_kanji'		=> __('お名前'),
			'name_kana'			=> __('フリガナ'),
			'phone'				=> __('電話番号'),
			'email'				=> __('メールアドレス'),
			'content'			=> __('お問合せ内容'),
			'content_etc'		=> __('お問合せ内容(その他)'),
			'class_room'		=> __('生徒の学年'),
			'class_room_etc'	=> __('生徒の学年(その他)'),
			'created_at'		=> __('作成日')
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$sortable_columns = array();

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions = [
			'bulk-delete' => '削除'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items()
	{

		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page('form_per_page', 10);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_dataform($per_page, $current_page);
	}

	public function process_bulk_action()
	{
		if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
			|| (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
		) {

			$delete_ids = esc_sql($_POST['bulk-delete']);

			foreach ($delete_ids as $id) {
				self::delete_data_form($id);
			}

			wp_redirect(esc_url_raw(add_query_arg()));
			exit;
		}
	}
}

class SP_Plugin_Care
{

	// class instance
	static $instance;

	//  WP_List_Table object
	public $care_form_obj;

	// class constructor
	public function __construct()
	{
		add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
		add_action('admin_menu', [$this, 'plugin_menu']);
	}


	public static function set_screen($status, $option, $value)
	{
		return $value;
	}

	public function plugin_menu()
	{

		$hook = add_menu_page(
			'Contact Data',
			'Contact Data',
			'manage_options',
			'data_care_form',
			[$this, 'plugin_settings_page']
		);

		add_action("load-$hook", [$this, 'screen_option']);
	}

	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page()
	{
		global $wpdb;

		$selectData = "SELECT * FROM wp8_contact_setting";
		$result = $wpdb->get_results($selectData, 'ARRAY_A');
?>
		<div class="wrap">
			<h2>Manager data form</h2>
			<style>
				.button-custom {
					font-size: 13px;
					line-height: 2.15384615;
					min-height: 30px;
					margin: 0 0 30px;
					padding: 0 10px;
					cursor: pointer;
					border-width: 1px;
					border-style: solid;
					border-radius: 3px;
					color: #0071a1;
					border-color: #0071a1;
					background: #f3f5f6;
				}
				
				label {
					font-size: 20px;
					margin-bottom: 10px;
					display: block;
				}
				
				textarea {
					margin-bottom: 15px;
				}
			</style>
			<button id="showBoxChangePolicy" class="button-custom">Change policy/inquiry</button>
			<form method="post" id="FormPolicy" class="hidden">
			    <a href="https://codebeautify.org/htmlviewer" target="_blank">View content HTML</a><br />
				<label>Policy</label>
				<textarea name="policy" id="policyData" rows="14" style="padding: 20px;width: 100%;"><?php echo $result[0]['policy'] ?></textarea>
				<label>Email content</label>
				<ul>
					<li>お名前 -> {{customer_name}}</li>
					<li>フリガナ -> {{customer_kana}}</li>
					<li>電話番号 -> {{tel}}</li>
					<li>メールアドレス -> {{email}}</li>
					<li>お問合せ内容 -> {{content}}</li>
					<li>お問合せ内容(その他) -> {{content-etc}}</li>
					<li>生徒の学年 -> {{class_room}}</li>
					<li>生徒の学年(その他) -> {{class_room-etc}}</li>
					<li>生徒の学校 -> {{gakkou}}</li>
				</ul>
				<textarea name="email_content" id="emailData" rows="14" style="padding: 20px;width: 100%;"><?php echo $result[0]['email_content'] ?></textarea>
				<label>Inquiry</label>
				<textarea name="inquiry" id="inquiryData" rows="5" style="padding: 20px;width: 100%;"><?php echo $result[0]['inquiry'] ?></textarea>
				<label>Thanks content</label>
				<textarea name="thanks_content" id="thanksData" rows="8" style="padding: 20px;width: 100%;"><?php echo $result[0]['thanks_content'] ?></textarea>
				<button type="submit" class="button-custom" style="color: white; background-color: blueviolet;">Update</button>
			</form>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->care_form_obj->prepare_items();
								$this->care_form_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
<?php }

	/**
	 * Screen options
	 */
	public function screen_option()
	{

		$option = 'per_page';
		$args   = [
			'label'   => 'Total',
			'default' => 10,
			'option'  => 'form_per_page'
		];

		add_screen_option($option, $args);

		$this->care_form_obj = new Care_List();
	}


	/** Singleton instance */
	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}


add_action('plugins_loaded', function () {
	SP_Plugin_Care::get_instance();
});
