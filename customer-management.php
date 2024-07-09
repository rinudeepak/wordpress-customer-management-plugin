<?php

/*
  Plugin Name: Customer Management
  Version: 1.0
  Author: Rinu
  
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CustomerManagementPlugin {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "customer";

    add_action('activate_customer-management/customer-management.php', array($this, 'onActivate'));
    add_action('admin_menu', array($this, 'ourMenu'));
    add_action('admin_post_add_customer', array($this, 'addCustomer'));
    add_action('admin_post_edit_customer', array($this, 'editCustomer'));
    add_action('admin_post_delete_customer', array($this, 'deleteCustomer'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('wp_ajax_nopriv_load_customers', 'load_customers');
    add_action('wp_ajax_load_customers', 'load_customers');
    add_shortcode('customer_list_shortcode', array($this, 'render_customer_list_shortcode')); //you can use the [customer_list_shortcode] shortcode anywhere in your WordPress site 
    
  }
  public function enqueue_scripts() {
    wp_enqueue_script('customer-list-ajax', plugins_url('/js/customer-list-ajax.js', __FILE__), array('jquery'), '1.0', true);
wp_localize_script('customer-list-ajax', 'customerListAjax', array(
      'ajaxurl' => admin_url('admin-ajax.php'),
  ));
  

  }

  function load_customers() {
    include(plugin_dir_path(__FILE__) . 'template/template-customer.php');
    wp_die(); // This is required to terminate immediately and return a proper response.
  }

  function ourMenu() {
    add_menu_page('Customer Management', 'Customer Management', 'manage_options', 'customer-management', array($this, 'customerListPage'), 'dashicons-smiley', 100);
    add_submenu_page('customer-management', 'Add New Customer', 'Add New Customer', 'manage_options', 'add-customer', array($this, 'addCustomerPage'));
    add_submenu_page(null, 'Edit Customer', 'Edit Customer', 'manage_options', 'edit-customer', array($this, 'editCustomerPage'));
  }

  
  function customerListPage() { 
    global $wpdb;
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = $search ? "WHERE customer_name LIKE '%$search%' OR email LIKE '%$search%' OR phone_no LIKE '%$search%' OR cr_no LIKE '%$search%' OR customer_status LIKE '%$search%'" : '';

    $results = $wpdb->get_results("SELECT * FROM $this->tablename $where LIMIT $limit OFFSET $offset");
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $this->tablename $where");

    echo '<div class="wrap">';
    echo '<h1>Customer Management</h1>';
    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="customer-management">';
    echo '<input type="text" name="s" value="' . esc_attr($search) . '">';
    echo '<input type="submit" value="Search" class="button">';
    echo '</form>';
    echo '<a href="?page=add-customer" class="button">Add New Customer</a>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Date of Birth</th><th>Age</th><th>Gender</th><th>CR Number</th><th>Address</th><th>City</th><th>Country</th><th>Status</th><th>Actions</th></tr></thead>';
    echo '<tbody>';
    if ($results) {
      foreach ($results as $row) {
        $dob = new DateTime($row->dob);
        $now = new DateTime();
        $age = $dob->diff($now)->y;

        echo '<tr>';
        echo '<td>' . esc_html($row->id) . '</td>';
        echo '<td>' . esc_html($row->customer_name) . '</td>';
        echo '<td>' . esc_html($row->email) . '</td>';
        echo '<td>' . esc_html($row->phone_no) . '</td>';
        echo '<td>' . esc_html($row->dob) . '</td>';
        echo '<td>' . esc_html($age) . '</td>';
        echo '<td>' . esc_html($row->gender) . '</td>';
        echo '<td>' . esc_html($row->cr_no) . '</td>';
        echo '<td>' . esc_html($row->customer_address) . '</td>';
        echo '<td>' . esc_html($row->city) . '</td>';
        echo '<td>' . esc_html($row->country) . '</td>';
        echo '<td>' . esc_html($row->customer_status) . '</td>';
        echo '<td><a href="?page=edit-customer&id=' . esc_attr($row->id) . '">Edit</a> | <a href="' . admin_url('admin-post.php') . '?action=delete_customer&id=' . esc_attr($row->id) . '" onclick="return confirm(\'Are you sure you want to delete this customer?\')">Delete</a></td>';
        echo '</tr>';
      }
    } else {
      echo '<tr><td colspan="12">No customers found.</td></tr>';
    }
    echo '</tbody></table>';

    $total_pages = ceil($total_customers / $limit);
    echo '<div class="tablenav"><div class="tablenav-pages">';
    echo paginate_links(array(
      'base' => add_query_arg('paged', '%#%'),
      'format' => '',
      'prev_text' => __('&laquo;'),
      'next_text' => __('&raquo;'),
      'total' => $total_pages,
      'current' => $page,
    ));
    echo '</div></div>';
    echo '</div>';


  }

  function addCustomerPage() {
    echo '<div class="wrap"><h1>Add New Customer</h1>';
    echo '<form action="' . admin_url('admin-post.php') . '" method="post">';
    echo '<input type="hidden" name="action" value="add_customer">';
    wp_nonce_field('add_customer_nonce');
    echo '<table class="form-table">';
    echo '<tr><th><label for="customer_name">Name</label></th><td><input type="text" name="customer_name" id="customer_name" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="email">Email</label></th><td><input type="email" name="email" id="email" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="phone_no">Phone</label></th><td><input type="text" name="phone_no" id="phone_no" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="dob">Date of Birth</label></th><td><input type="date" name="dob" id="dob" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="gender">Gender</label></th><td><input type="text" name="gender" id="gender" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="cr_no">CR Number</label></th><td><input type="text" name="cr_no" id="cr_no" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="customer_address">Address</label></th><td><input type="text" name="customer_address" id="customer_address" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="city">City</label></th><td><input type="text" name="city" id="city" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="country">Country</label></th><td><input type="text" name="country" id="country" class="regular-text" required></td></tr>';
    echo '<tr><th><label for="customer_status">Status</label></th><td><select name="customer_status" id="customer_status" required><option value="active">Active</option><option value="inactive">Inactive</option></select></td></tr>';
    echo '</table>';
    echo '<p class="submit"><input type="submit" value="Add Customer" class="button button-primary"></p>';
    echo '</form></div>';
  }

  function addCustomer() {
    check_admin_referer('add_customer_nonce');
    global $wpdb;
    $customer_name = sanitize_text_field($_POST['customer_name']);
    $email = sanitize_email($_POST['email']);
    $phone_no = sanitize_text_field($_POST['phone_no']);
    $dob = sanitize_text_field($_POST['dob']);
    $gender = sanitize_text_field($_POST['gender']);
    $cr_no = sanitize_text_field($_POST['cr_no']);
    $customer_address = sanitize_text_field($_POST['customer_address']);
    $city = sanitize_text_field($_POST['city']);
    $country = sanitize_text_field($_POST['country']);
    $customer_status = sanitize_text_field($_POST['customer_status']);

    // Check if email already exists
    if (email_exists($email)) {
        wp_redirect(admin_url('admin.php?page=customer-management&error=email_exists'));
        exit;
    }

    // Create new WordPress user
    $userdata = array(
        'user_login' => $email,
        'user_email' => $email,
        'user_pass'  => $phone_no,
        'role'       => 'contributor'
    );
    $user_id = wp_insert_user($userdata);

    // Check if user creation was successful
    if (is_wp_error($user_id)) {
        wp_redirect(admin_url('admin.php?page=customer-management&error=user_creation_failed'));
        exit;
    }

    // Insert customer data into custom table
    $data = array(
        'customer_name' => $customer_name,
        'email' => $email,
        'phone_no' => $phone_no,
        'dob' => $dob,
        'gender' => $gender,
        'cr_no' => $cr_no,
        'customer_address' => $customer_address,
        'city' => $city,
        'country' => $country,
        'customer_status' => $customer_status,
    );
    $wpdb->insert($this->tablename, $data);

    wp_redirect(admin_url('admin.php?page=customer-management&success=customer_added'));
    exit;

  }

  function editCustomerPage() {
    global $wpdb;
    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $customer = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->tablename WHERE id = %d", $id));
    if (!$customer) {
      wp_die('Customer not found.');
    }

    echo '<div class="wrap"><h1>Edit Customer</h1>';
    echo '<form action="' . admin_url('admin-post.php') . '" method="post">';
    echo '<input type="hidden" name="action" value="edit_customer">';
    echo '<input type="hidden" name="id" value="' . esc_attr($id) . '">';
    wp_nonce_field('edit_customer_nonce');
    echo '<table class="form-table">';
    echo '<tr><th><label for="customer_name">Name</label></th><td><input type="text" name="customer_name" id="customer_name" class="regular-text" value="' . esc_attr($customer->customer_name) . '" required></td></tr>';
    echo '<tr><th><label for="email">Email</label></th><td><input type="email" name="email" id="email" class="regular-text" value="' . esc_attr($customer->email) . '" required></td></tr>';
    echo '<tr><th><label for="phone_no">Phone</label></th><td><input type="text" name="phone_no" id="phone_no" class="regular-text" value="' . esc_attr($customer->phone_no) . '" required></td></tr>';
    echo '<tr><th><label for="dob">Date of Birth</label></th><td><input type="date" name="dob" id="dob" class="regular-text" value="' . esc_attr($customer->dob) . '" required></td></tr>';
    echo '<tr><th><label for="gender">Gender</label></th><td><input type="text" name="gender" id="gender" class="regular-text" value="' . esc_attr($customer->gender) . '" required></td></tr>';
    echo '<tr><th><label for="cr_no">CR Number</label></th><td><input type="text" name="cr_no" id="cr_no" class="regular-text" value="' . esc_attr($customer->cr_no) . '" required></td></tr>';
    echo '<tr><th><label for="customer_address">Address</label></th><td><input type="text" name="customer_address" id="customer_address" class="regular-text" value="' . esc_attr($customer->customer_address) . '" required></td></tr>';
    echo '<tr><th><label for="city">City</label></th><td><input type="text" name="city" id="city" class="regular-text" value="' . esc_attr($customer->city) . '" required></td></tr>';
    echo '<tr><th><label for="country">Country</label></th><td><input type="text" name="country" id="country" class="regular-text" value="' . esc_attr($customer->country) . '" required></td></tr>';
    echo '<tr><th><label for="customer_status">Status</label></th><td><select name="customer_status" id="customer_status" required>';
    echo '<option value="active" ' . selected($customer->customer_status, 'active', false) . '>Active</option>';
    echo '<option value="inactive" ' . selected($customer->customer_status, 'inactive', false) . '>Inactive</option>';
    echo '</select></td></tr>';
    echo '</table>';
    echo '<p class="submit"><input type="submit" value="Update Customer" class="button button-primary"></p>';
    echo '</form></div>';
  }

  function editCustomer() {
    check_admin_referer('edit_customer_nonce');
    global $wpdb;
    $id = absint($_POST['id']);
    $data = array(
      'customer_name' => sanitize_text_field($_POST['customer_name']),
      'email' => sanitize_email($_POST['email']),
      'phone_no' => sanitize_text_field($_POST['phone_no']),
      'dob' => sanitize_text_field($_POST['dob']),
      'gender' => sanitize_text_field($_POST['gender']),
      'cr_no' => sanitize_text_field($_POST['cr_no']),
      'customer_address' => sanitize_text_field($_POST['customer_address']),
      'city' => sanitize_text_field($_POST['city']),
      'country' => sanitize_text_field($_POST['country']),
      'customer_status' => sanitize_text_field($_POST['customer_status']),
    );
    $where = array('id' => $id);
    $wpdb->update($this->tablename, $data, $where);
    wp_redirect(admin_url('admin.php?page=customer-management'));
    exit;
  }

  function deleteCustomer() {
    if (current_user_can('administrator')) {
      $id = absint($_GET['id']);
      global $wpdb;
      $wpdb->delete($this->tablename, array('id' => $id));
      wp_redirect(admin_url('admin.php?page=customer-management'));
    } else {
      wp_die(__('You do not have sufficient permissions to access this page.'));

    }
    exit;
  }



  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      customer_name varchar(255) NOT NULL DEFAULT '',
      email varchar(255) NOT NULL DEFAULT '',
      phone_no varchar(20) NOT NULL,
      dob date NOT NULL,
      gender varchar(60) NOT NULL DEFAULT '',
      cr_no varchar(60) NOT NULL DEFAULT '',
      customer_address varchar(255) NOT NULL DEFAULT '',
      city varchar(100) NOT NULL DEFAULT '',
      country varchar(100) NOT NULL DEFAULT '',
      customer_status enum('active', 'inactive') NOT NULL DEFAULT 'active',
      PRIMARY KEY  (id),
      CONSTRAINT chk_phone_no CHECK (phone_no REGEXP '^[0-9]+$'),
      CONSTRAINT chk_cr_no CHECK (cr_no REGEXP '^[a-zA-Z0-9]+$')
    ) $this->charset;");
  }

  public function render_customer_list_shortcode() {
    ob_start(); // Start output buffering
    include(plugin_dir_path(__FILE__) . 'template/template-customer.php'); // Path to your template file
    return ob_get_clean(); // Return the buffered content
  }



}

$CustomerManagementPlugin = new CustomerManagementPlugin();