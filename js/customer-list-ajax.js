jQuery(document).ready(function($) {
  $(document).on('click', '.tablenav-pages a.page-numbers', function(e) {
      e.preventDefault();

      var clickedLink = $(this);
      var link = clickedLink.attr('href');
      var page = clickedLink.text(); // Extract the page number from the link text

      $.ajax({
          url: customerListAjax.ajax_url,
          type: 'POST',
          data: {
              action: 'load_customers',
              paged: page,
              ajax: 1
          },
          beforeSend: function() {
              // Add a loading indicator if needed
          },
          success: function(data) {
              var newContent = $(data).find('.customer-list-table').html();
              $('.customer-list-table').html(newContent);

              // Update pagination links
              var newPaginationLinks = $(data).find('.tablenav-pages').html();
              $('.tablenav-pages').html(newPaginationLinks);
          },
          complete: function() {
              // Highlight the clicked page link
              clickedLink.addClass('active').siblings().removeClass('active');
          },
          error: function(xhr, status, error) {
              console.error('AJAX Error:', status, error);
          }
      });
  });
});
