$(function () {

  var w = $('.search__aiueo-item a');
  var ww = w.innerWidth();

  var h = ww + 8;

  $('.search__aiueo-item a').css('height',ww);

  $('.search__aiueo-list').css('height',h)

  $(".search__show-more").click(function() {
    var show_text = $(this)
      .parent(".search__aiueo")
      .find(".search__aiueo-list");
    var small_height = h; //This is initial height.
    var original_height = show_text.css({ height: "auto" }).height();

    if (show_text.hasClass("open")) {
      /*CLOSE*/
      show_text.height(original_height).animate({ height: small_height }, 300);
      show_text.removeClass("open");
      $(this)
        .text("さらに表示")
        .removeClass("active");
    } else {
      /*OPEN*/
      show_text
        .height(small_height)
        .animate({ height: original_height }, 300, function() {
          show_text.height("auto");
        });
      show_text.addClass("open");
      $(this)
        .text("閉じる")
        .addClass("active");
    }
  });


});