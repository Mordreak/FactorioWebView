function popupMe(content) {
    var popup = $('#popup-notification');
    $('#popup-notification span').text(content);
    popup.fadeIn({queue: false, duration: 'normal'});
    popup.animate({top: '30px'}, 'normal');
    setTimeout(function() {
        popup.fadeOut('fast');
    }, 4000, function() {
        popup.text('');
        popup.css('top', 0);
    });
}