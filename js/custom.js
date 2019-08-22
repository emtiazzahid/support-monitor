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

          location.reload(true);
        }
    });
  });

  $(document).on('submit','#update_form', function(e) {
    e.preventDefault();
    $('.wqmessage').html('');
    $('.wqsubmit_message').html('');

    var wqslug = $('#wqslug').val();

    if(wqslug=='') {
      $('#wqslug_message').html('Slug is Required');
    }

    $.ajax({
      data: {
          slug: wqslug,
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

        location.reload(true);
      }
    });
  });

  $(document).on('change','#plugin, #hour_before', function(e) {
    e.preventDefault();

    var plugin = $('#plugin').val();

    var hour_before = $('#hour_before').val();
    if (hour_before == "") {
        alert('Hour field is required');
        return false;
    }

    $.ajax({
      data: {
          plugin: plugin,
          hour_before: hour_before,
          action: 'wqplugin_data_fetch'
      },
      type: 'GET',
      url: ajaxurl,
      success: function(response) {
          $('#plugin_title').text('');
          $('#plugin_issue_table').empty();
          let htmlString = '<tr>' +
              '<td colspan="4">NO DATA AVAILABLE</td></tr>';

          let result = JSON.parse(response);
          if (result.rescode === 200) {

              if (result.data.plugin_title === 'WordPress.org Forums » All Topics'){
                  alert('Plugin not found! Please recheck the slug of plugin');
              } else{
                  $('#plugin_title').text(result.data.plugin_title);
              }

              if (result.data.issues.length > 0) {
                  htmlString = '';
                  for (let i = 0; i < result.data.issues.length; i++) {
                      htmlString += '<tr>' +
                          '<td><a target="_blank" href="' + result.data.issues[i].link + '">' + result.data.issues[i].title + '</a></td>' +
                          '<td>' + result.data.issues[i].pubDate +'( '+moment(result.data.issues[i].pubDate).fromNow()+' )'+ '</td>' +
                          '<td>' + result.data.issues[i].creator + '</td>' +
                          '</tr>';
                  }
              }
          }else{
              alert(response);
          }

          $('#plugin_issue_table').append(htmlString);
      }
    });
  });



});
