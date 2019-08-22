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

    alert('asdasdas');

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
      requestToFetchData();
  });

  function requestToFetchData() {
      var plugin = $('#plugin').val();

      var hour_before = $('#hour_before').val();
      if (hour_before === "" || !hour_before) {
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
          success: function (response) {
              appendPluginInfoTableData(response);
          }
      });
  }

  function appendPluginInfoTableData(response) {
    $('#plugin_issue_table').empty();
    let htmlString = '<tr><td colspan="4">NO DATA AVAILABLE</td></tr>';

    let result = JSON.parse(response);

    if (result.length > 0){
        htmlString = '';
        for (let i = 0; i < result.length; i++) {
            if (result[i].issues.length > 0) {
                for (let j = 0; j < result[i].issues.length; j++) {
                    let rowSpanElement = '';
                    if (j === 0){
                        rowSpanElement = '<td rowspan="'+result[i].issues.length+'">' + result[i].issues[j].slug + '</td>';
                    }
                    htmlString += '<tr>' +
                        rowSpanElement+
                        '<td><a target="_blank" href="' + result[i].issues[j].link + '">' + result[i].issues[j].title + '</a></td>' +
                        '<td>' + result[i].issues[j].pubDate + '( ' + moment(result[i].issues[j].pubDate).fromNow() + ' )' + '</td>' +
                        '<td>' + result[i].issues[j].creator + '</td>' +
                        '</tr>';
                }
            }else{
                htmlString += '<tr><td>'+result[i].slug+'</td><td colspan="3">'+result[i].status+'</td></tr>';
            }
        }
    }

    $('#plugin_issue_table').append(htmlString);
  }

    requestToFetchData();
});

