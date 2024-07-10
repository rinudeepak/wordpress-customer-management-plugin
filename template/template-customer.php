<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Fetch customers from the database.
global $wpdb;
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$page = isset($_GET['page']) ? absint($_GET['page']) : 1;

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
// Fetch customers for the current page
$customers = $wpdb->get_results("SELECT * FROM $tablename $where ORDER BY id ASC LIMIT $limit OFFSET $offset");

// Display the customers.
if ($customers) :
  echo '<div id="customer-search-container">';
  echo '<input type="text" id="customer-search" placeholder="Search customers...">';
  echo '</div>';
  echo '<div id="customer-list">';
  echo '<div id="customer-table-list">';
  echo '<table class="customer-list-table">';
  echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Date of Birth</th><th>Age</th><th>Gender</th><th>CR Number</th><th>Address</th><th>City</th><th>Country</th><th>Status</th></tr></thead>';
  echo '<tbody>';
  foreach ($customers as $row) {
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
  echo '</div>';
    // Pagination
    $total_pages = ceil($total_customers / $limit);
    if ($total_pages > 1): 
      echo '<div class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++): 
          echo '<a href="#" class="page-link" data-page="'.$i.'">'.$i.'</a>';
        endfor; 
      echo '</div>';
    endif; 
  else: 
    echo '<p>No customers found.</p>';
  endif; 

  echo '</div>';

?>
