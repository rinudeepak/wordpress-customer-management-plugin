<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Fetch customers from the database.
global $wpdb;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$page = isset($_POST['paged']) ? absint($_POST['paged']) : 1; // Adjust to POST data for AJAX
$limit = 10;
$offset = ($page - 1) * $limit;

// Combine search condition with the active status condition
$where = "WHERE customer_status = 'active'";
if ($search) {
    $where .= " AND (customer_name LIKE '%$search%' OR email LIKE '%$search%' OR phone_no LIKE '%$search%' OR cr_no LIKE '%$search%' OR customer_status LIKE '%$search%')";
}
$tablename = $wpdb->prefix . "customer";
$results = $wpdb->get_results("SELECT * FROM $tablename $where LIMIT $limit OFFSET $offset");
$total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $tablename $where");

// Display the customers.
if ($results) {
  echo '<form method="get">';
    echo '<input type="hidden" name="page" value="customer-management">';
    echo '<input type="text" name="s" value="' . esc_attr($search) . '">';
    echo '<input type="submit" value="Search" class="button">';
    echo '</form>';
  echo '<table class="customer-list-table">';
  echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Date of Birth</th><th>Age</th><th>Gender</th><th>CR Number</th><th>Address</th><th>City</th><th>Country</th><th>Status</th></tr></thead>';
  echo '<tbody>';
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
    echo '</tr>';
  }
  echo '</tbody></table>';
  $total_pages = ceil($total_customers / $limit);
  echo '<div class="tablenav"><div class="tablenav-pages">';
  echo paginate_links(array(
    'base' => '',
    'format' => '?paged=%#%', // Adjust format for AJAX handling
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => $total_pages,
    'current' => $page,
  ));
  echo '</div></div>';
} else {
  echo '<p>No customers found.</p>';
}

// If this is an AJAX request, exit after rendering the content
if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
  exit;
}
?>
