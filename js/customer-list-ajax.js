jQuery(document).ready(function($) {
    function loadCustomers(page, search) {
      $.ajax({
        url: customer_ajax_object.ajax_url,
        type: 'POST',
        data: {
          action: 'load_customers',
          nonce: customer_ajax_object.nonce,
          page: page,
          search: search
        },
        beforeSend: function() {
          $('#customer-list').addClass('loading');
        },
        success: function(response) {
          $('#customer-list').removeClass('loading');
          $('#customer-list').html(response.data.customers);
          $('.pagination').replaceWith(response.data.pagination);
        },
        error: function() {
          console.log('Error loading customers.');
        }
      });
    }
  
    // Handle pagination click
    $('#customer-list').on('click', '.page-link', function(e) {
      e.preventDefault();
      var page = $(this).data('page');
      var search = $('#customer-search').val();
      loadCustomers(page, search);
    });
  
    // Handle search input
    $('#customer-search').on('keyup', function() {
      var page = 1; // Reset to page 1 on search
      var search = $(this).val();
      loadCustomers(page, search);
    });
  });
  