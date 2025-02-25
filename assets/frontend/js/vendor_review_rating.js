/* global wcmb_seller_review_rating_js_script_data */

jQuery(document).ready(function ($) {
    $('#wcmb_vendor_reviews #respond #rating').hide().before('<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>');

    $('body')
            .on('click', '#wcmb_vendor_reviews #respond p.stars a', function () {
                var $star = $(this),
                        $rating = $(this).closest('#respond').find('#rating'),
                        $container = $(this).closest('.stars');
                $rating.val($star.text());
                $star.siblings('a').removeClass('active');
                $star.addClass('active');
                $container.addClass('selected');
                return false;
            })
            .on('click', '#wcmb_vendor_reviews #respond #submit', function () {
                var $rating = $(this).closest('#respond').find('#rating'),
                        rating = $rating.val();
                if ($rating.size() > 0 && !rating) {
                    window.alert(wcmb_seller_review_rating_js_script_data.messages.rating_error_msg_txt);
                    return false;
                }
                var comment = $('#wcmb_vendor_reviews #respond #comment').val();
                if (comment == '' || comment.length < 10) {
                    window.alert(wcmb_seller_review_rating_js_script_data.messages.review_error_msg_txt);
                    return false;
                }
                var vendor_id = $('#wcmb_vendor_reviews #respond #wcmb_vendor_for_rating').val();
                var data = {
                    action: 'wcmb_add_review_rating_vendor',
                    rating: rating,
                    comment: comment,
                    vendor_id: $('#wcmb_vendor_for_rating').val()
                }
                $.post(wcmb_seller_review_rating_js_script_data.ajax_url, data, function (response) {
                    if (response == 1) {
                        $('#wcmb_vendor_reviews #respond p#wcmb_seller_review_rating')
                                .html(wcmb_seller_review_rating_js_script_data.messages.review_success_msg_txt)
                                .addClass('success_review_msg')
                                .removeClass('error_review_msg');
                        $rating.val('');
                        $('#wcmb_vendor_reviews #respond #comment').val('');
                        $(".stars").removeClass('selected');
                        setTimeout(location.reload(), 2000);
                    } else {
                        $('#wcmb_vendor_reviews #respond p#wcmb_seller_review_rating')
                                .html(wcmb_seller_review_rating_js_script_data.messages.review_failed_msg_txt)
                                .addClass('error_review_msg')
                                .removeClass('success_review_msg');
                    }
                });

            });
    $('input#wcmb_review_load_more').click(function (e) {
        var pageno = $('#vendor_review_rating_pagi_form #wcmb_review_rating_pageno');
        var postperpage = $('#vendor_review_rating_pagi_form #wcmb_review_rating_postperpage');
        var totalpage = $('#vendor_review_rating_pagi_form #wcmb_review_rating_totalpage');
        var totalreview = $('#vendor_review_rating_pagi_form #wcmb_review_rating_totalreview');
        var term_id = $('#vendor_review_rating_pagi_form #wcmb_review_rating_term_id');
        $('.wcmb_review_loader').show();
        var data = {
            action: 'wcmb_load_more_review_rating_vendor',
            pageno: pageno.val(),
            postperpage: postperpage.val(),
            totalpage: totalpage.val(),
            totalreview: totalreview.val(),
            term_id: term_id.val()
        }
        $.post(wcmb_seller_review_rating_js_script_data.ajax_url, data, function (response) {
            $('ol.vendor_comment_list').append(response);
            pageno.val(parseInt(pageno.val()) + parseInt('1'));
            if (parseInt(pageno.val()) >= parseInt(totalpage.val())) {
                $('input#wcmb_review_load_more').hide();
            }
            $('.wcmb_review_loader').hide();
        });
    });

    $('body').on('click','.wcmb-comment-reply', function () {
        var comment_id = $(this).data('comment_id');
        var vendor_id = $(this).data('vendor_id');
        var comment = $('#comment-content-' + comment_id).val();
        if (comment === '' || comment.length < 10) {
            window.alert(wcmb_seller_review_rating_js_script_data.messages.review_error_msg_txt);
            return false;
        }
        var data = {
            action: 'wcmb_add_review_rating_vendor',
            comment: comment,
            vendor_id: vendor_id,
            comment_parent : comment_id
        };
        $.post(wcmb_seller_review_rating_js_script_data.ajax_url, data, function (response) {
            if (response == 1) {
                $('#commient-modal-'+comment_id).modal('hide');
                setTimeout(location.reload(), 2000);
            } else {
                
            }
        });
    });

});