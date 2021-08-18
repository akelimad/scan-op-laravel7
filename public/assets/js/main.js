function get_social_counts(action, url) {
    $.ajax({
        type: "GET",
        url: action,
        data: {thisUrl: url},
        dataType: "json",
        success: function(data) {
            $('.social-twitter').find('.follow-counter').html(data.twitter);
            $('.social-facebook').find('.follow-counter').html(data.facebook);
            $('.social-google').find('.follow-counter').html(data.gplus);
        }
    });
}