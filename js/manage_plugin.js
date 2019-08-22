jQuery(document).ready(function($){
    $body = $("body");

    $(document).on({
        ajaxStart: function() { $body.addClass("loading");    },
        ajaxStop: function() { $body.removeClass("loading"); }
    });

  $(document).on('submit','#entry_form', function(e) {
    e.preventDefault();
    $('.wqmessage').html('');
    $('.wqsubmit_message').html('');

    var wqslug = $('#wqslug').val();
    if(wqslug=='') {
      $('#wqslug_message').html('Slug is Required');
    }

     $.ajax({
        data: {
          'slug': wqslug,
          'action': 'wqnew_entry'
        },
        type: 'POST',
        url: ajaxurl,
        success: function(response) {
          var res = JSON.parse(response);
          $('.wqsubmit_message').html(res.message);
          if(res.rescode!='404') {
            $('#entry_form')[0].reset();
            $('.wqsubmit_message').css('color','green');
          } else {
            $('.wqsubmit_message').css('color','red');
          }

        }
    });
  });

  $(document).on('submit','#update_form', function(e) {
    e.preventDefault();
    $('.wqmessage').html('');
    $('.wqsubmit_message').html('');

    var wqslug = $('#wqslug').val();
    var wqentryid = $('#wqentryid').val();

    if(wqslug=='') {
      $('#wqslug_message').html('Slug is Required');
    }

    $.ajax({
      data: {
          slug: wqslug,
          wqentryid: wqentryid,
          action: 'wqedit_entry'
      },
      type: 'POST',
      url: ajaxurl,
      success: function(response) {
        var res = JSON.parse(response);
        $('.wqsubmit_message').html(res.message);
        if(res.rescode!='404') {
          $('.wqsubmit_message').css('color','green');
        } else {
          $('.wqsubmit_message').css('color','red');
        }

      }
    });
  });
});

