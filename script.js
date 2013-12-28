jQuery(document).ready(function () {

  // sets frequency of Craislist updates
  var pingTime = '300000';

  var startPing = function () {

    // create a deferred object
    var dfd = $.Deferred();

    // get our log of previously emailed CL posts and move the post titles into an array
    $.getJSON('log.json')
      .success(function (response) {

        if (response === null || response.length <= 0) {
          alert('json log is empty!');
        }

        var log = [];
        $.each(response, function(i, val) {
          log.push(val.title);
          if (i === (response.length - 1)) {
            dfd.resolve(log);
          }
        });

      })
      .error(function(){
        alert('couldn\'t load json log!');
      });

    // once we've got our log loaded, use google's feed api
    // to request the craiglist RSS feeds
    dfd
      .done(function(log) {

        $.getJSON('craigslist_settings.json')
          .success(function (response) {

            var config = response;
            var clQuery = '/search/cto?query=' + config.searchString +
                          '&catAbb=' + config.category +
                          '&srchType=A&minAsk=' + config.priceMin +
                          '&maxAsk=' + config.priceMax +
                          '&hasPic&format=rss';
            var url = '';

            $.each(config.clFeeds, function (i, val) {

              url = document.location.protocol +
                    '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' +
                    encodeURIComponent(val + clQuery);

              // store titles in array
              $.getJSON(url)
                .success(function (response) {

                  // process json results
                  processRssItem(i, val, response, log, config.clFeeds.length);

                })
                .error(function(){
                  alert('couldn\'t load json from google!');
                });

            });

          })
          .error(function(){
            alert('couldn\'t load craigslist settings!');
          });

      });

  };

  // this iterates through the feeds
  function processRssItem(i, val, data, log, count) {

    var postJson = {};

    if ((data.responseData.feed.entries).length) {

      $(data.responseData.feed.entries).each(function(i, val){

        if ($.inArray(val.title, log) > -1 ) {
          // if the cl post has already been logged do nothing
        }
        else {
          // if it's not in the list then we need to log it
          postJson = {
            title: val.title,
            url: val.link,
            dateStamp: new Date(val.publishedDate),
            content: val.content
          };
          triggerLog(postJson);
          // and then we should email it
          triggerEmail(postJson);
        }

      });

    }

    // last time through the loop
    if (i === count - 1) {

      // change background color
      $('body').css('background-color', randomHex());

      // start the whole process over again
      setTimeout(function(){
        startPing();
      }, pingTime);

    }

  }

  function randomHex() {
    return '#' + ('000000' + Math.floor(Math.random() * 0xFFFFFF).toString(16)).substr(-6);
  }

  function triggerLog(json) {

    $.ajax({
      type: 'POST',
      url: 'logger.php',
      data: JSON.stringify(json, null, 0),
      contentType: 'application/json',
      success: function (data){
        // do nothing
      },
      error: function () {
        alert('Couldn\'t connect to the server');
      }
    });

  }

  function triggerEmail(json) {

    var emailLog = $('#emailLog');
    var item;

    $.ajax({
      type: 'POST',
      url: 'emailer.php',
      data: JSON.stringify(json, null, 0),
      contentType: 'application/json',
      success: function(data){
        item = $('<li>Email sent for <a href="' + json.url + '">' + json.title + '</a></li>');
        item.prependTo(emailLog).fadeIn('slow');
      },
      error: function () {
        alert('Couldn\'t connect to the server');
      }
    });

  }

  // start things off
  startPing();

});