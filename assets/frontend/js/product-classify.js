/* global wcmb_product_classify_script_data */
(function ($) {
    
    // wcmb_product_classify_script_data is required to continue, ensure the object exists
    if ( typeof wcmb_product_classify_script_data === 'undefined' ) {
        return false;
    }
    
    var block = function( $node ) {
        if ( ! is_blocked( $node ) ) {
            $node.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
        }
    };
    
    var is_blocked = function( $node ) {
        return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    var unblock = function( $node ) {
        $node.removeClass( 'processing' ).unblock();
    };
    
    // Common scroll to element code.
    var scroll_to = function( scrollElement ) {
        //if ( scrollElement.length ) {
            $( 'html, body' ).animate( {
                    scrollTop: ( scrollElement.offset().top - 100 )
            }, 1000 );
        //}
    };
    
    var wcmb_product_classify = {
        $search_by_name_gtin_form: $( 'form.search-pro-by-name-gtin' ),
        $searched_products_name_gtin_panel: $( '.searched-products-name-gtin-panel' ),
        $categories_search_wrapper: $( '.categories-search-wrapper' ),
        $searched_categories_results: $( '#searched-categories-results' ),
        $product_categories_wrap: $( '.wcmb-product-categories-wrap' ),
        $edit_product_form: $('#wcmb-edit-product-form'),
        init: function() {
            this.$search_by_name_gtin_form.on( 'click', '.search-product-name-gtin-btn', this.show_products_searched_by_name_gtin.bind( this ) );
            this.$search_by_name_gtin_form.on( 'keypress', '.search-product-name-gtin-keyword', this.bind_show_products_searched_by_name_gtin.bind( this ) );
            this.$categories_search_wrapper.on( 'input', '#search-categories-keyword', this.show_searched_categories );
            this.$searched_categories_results.on( 'click', '.list-group-item', this.show_product_classify_next_level_from_searched_term );
            $( document.body ).on( 'click', '.wcmb-product-categories-wrap .wcmb-product-cat-level .cat-item a', this.show_product_classify_next_level );
            $( document.body ).on( 'click', '.wcmb-product-categories-wrap .classified-pro-cat-btn', this.set_classified_product_terms );
            $( document.body ).on( 'click', '.searched-result-products-name-gtin .wcmb-create-pro-duplicate-btn', this.create_duplicate_product );
            // helper functions
            this.$searched_products_name_gtin_panel.hide();
            this.$searched_categories_results.hide();
            this.$product_categories_wrap.append('<div class="wcmb-product-cat-level initial-graphic select-category-graphic">\n\
                                    <img src="'+wcmb_product_classify_script_data.initial_graphic_url+'" alt="select category">\n\
                                    <h1>'+wcmb_product_classify_script_data.i18n.select_cat_list+'</h1></div>');
        },
        
        show_products_searched_by_name_gtin: function() {
            block($( 'form.search-pro-by-name-gtin' ));
            var data = {
                action: 'wcmb_list_a_product_by_name_or_gtin',
                keyword: $( 'input.search-product-name-gtin-keyword' ).val()
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
                    if(response.results){
                        //$('.searched-products-name-gtin-panel').show();
                        $('.searched-result-products-name-gtin').html(response.results);
                    }else{
                        $('.searched-result-products-name-gtin').html('');
                    }
                },
                complete: function() {
                    $('.searched-products-name-gtin-panel').show();
                    unblock($( 'form.search-pro-by-name-gtin' ));
                    scroll_to($( '.searched-products-name-gtin-panel' ));
                }
            } );
        },
        bind_show_products_searched_by_name_gtin: function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                block($( 'form.search-pro-by-name-gtin' ));
                var data = {
                    action: 'wcmb_list_a_product_by_name_or_gtin',
                    keyword: $( 'input.search-product-name-gtin-keyword' ).val()
                };
                // Make ajax call.
                $.ajax( {
                    type:     'post',
                    url:      wcmb_product_classify_script_data.ajax_url,
                    data:     data,
                    success:  function( response ) {
                        if(response.results){
                            //$('.searched-products-name-gtin-panel').show();
                            $('.searched-result-products-name-gtin').html(response.results);
                        }else{
                            $('.searched-result-products-name-gtin').html('');
                        }
                    },
                    complete: function() {
                        $('.searched-products-name-gtin-panel').show();
                        unblock($( 'form.search-pro-by-name-gtin' ));
                        scroll_to($( '.searched-products-name-gtin-panel' ));
                    }
                } );
                event.preventDefault();
            }
        },
        show_searched_categories: function() {
            var keyword = $(this).val();
            var data = {
                action: 'wcmb_product_classify_search_category_level',
                keyword: keyword
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
                    $( '#searched-categories-results' ).html('');
                    if(response.results){
                        $( '#searched-categories-results' ).html(response.results);
                    }
                },
                complete: function() {
                    $( '#searched-categories-results' ).show();
                }
            } );
        },
        show_product_classify_next_level: function(event) {
            event.preventDefault();
            block( $( '.wcmb-product-categories-wrap' ) );            
            var $wrapper = $( this ).closest( '.wcmb-product-categories-wrap' );
            var $hierarchy = $( this ).parents().find( '.wcmb-product-category-hierarchy' );
            var $term_id = $( this ).parent().data( 'term-id' );
            var $taxonomy = $( this ).parent().data( 'taxonomy' );
            var $_this = $( this );
            var data = {
                action: 'wcmb_product_classify_next_level_list_categories',
                term_id: $term_id,
                taxonomy: $taxonomy,
                cat_level: $_this.closest('.wcmb-product-categories').data('cat-level')
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
                    if(response.hierarchy){
                        $hierarchy.html(response.hierarchy);
                    }
                    if(response.html_level){
                        $_this.parents().siblings( '.wcmb-product-cat-level.'+response.level+'-level-cat' ).nextAll().remove();
                        $_this.parents().siblings( '.wcmb-product-cat-level.'+response.level+'-level-cat' ).remove();

                        if(response.is_final){
                            $wrapper.append('<div class="wcmb-product-cat-level '+response.level+'-level-cat cat-column select-cat-button-holder" data-level="'+response.level+'" style="width: '+ get_cat_width +'px">'+ response.html_level +'</div>'); 
                            $(".wcmb-product-cat-level").mCustomScrollbar();
                            checkCategoryScroller();
                            
                        }else{
                            $wrapper.append('<div class="wcmb-product-cat-level '+response.level+'-level-cat cat-column" data-level="'+response.level+'" style="width: '+ get_cat_width +'px">'+ response.html_level +'</div>'); 
                            $(".wcmb-product-cat-level").mCustomScrollbar();
                            checkCategoryScroller();
                        }
                    }
                },
                complete: function() {
                    unblock($( '.wcmb-product-categories-wrap' ));
                    $( '.wcmb-product-categories-wrap .wcmb-product-cat-level.initial-graphic' ).hide();
                    $_this.parent().addClass('active').siblings().removeClass('active');
                }
            } );
        },
        show_product_classify_next_level_from_searched_term: function() {
            block( $( '#searched-categories-results' ) );
            var $wrapper = $( '.wcmb-product-categories-wrap' );
            var $hierarchy = $( this ).parents().find( '.wcmb-product-category-hierarchy' );
            var $term_id = $( this ).data( 'term-id' );
            var $taxonomy = $( this ).data( 'taxonomy' );
            var $_this = $( this );
            var data = {
                action: 'show_product_classify_next_level_from_searched_term',
                term_id: $term_id,
                taxonomy: $taxonomy
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
//                    if(response.hierarchy){
//                        $hierarchy.html(response.hierarchy);
//                    }
                    if(response.html_level){
                        $wrapper.html('');
                        $wrapper.append(response.html_level);
                        $(".wcmb-product-cat-level").mCustomScrollbar();
                        checkCategoryScroller();
                        var getAllCatColumn = $('.wcmb-product-categories-wrap .cat-column');
                        getAllCatColumn.css("width", get_cat_width);
                        
                    }
                },
                complete: function() {
                    scroll_to($( '.wcmb-categories-level-panel' ));
                    unblock($( '#searched-categories-results' ));
                    $( '#searched-categories-results' ).hide();
                    $( '.wcmb-product-categories-wrap .wcmb-product-cat-level.initial-graphic' ).hide();
                    //$_this.parent().addClass('active').siblings().removeClass('active');
                }
            } );
        },
        set_classified_product_terms: function () {
            block( $(this).parents('.wcmb-product-cat-level') );
            var $term_id = $( this ).data( 'term-id' );
            var $taxonomy = $( this ).data( 'taxonomy' );
            var $_this = $( this );
            var data = {
                action: 'wcmb_set_classified_product_terms',
                term_id: $term_id,
                taxonomy: $taxonomy
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
                    window.location.href = response.url;
                },
                complete: function() {
                    unblock($_this.parents('.wcmb-product-cat-level'));
                    
                }
            } );
        },
        create_duplicate_product: function () {
            block( $(this).parents('.searched-result-products-name-gtin') );
            var $product_id = $( this ).data( 'product_id' );
            var $_this = $( this );
            var data = {
                action: 'wcmb_create_duplicate_product',
                product_id: $product_id
            };
            // Make ajax call.
            $.ajax( {
                type:     'post',
                url:      wcmb_product_classify_script_data.ajax_url,
                data:     data,
                success:  function( response ) {
                    window.location.href = response.redirect_url;
                },
                complete: function() {
                    unblock($_this.parents('.searched-result-products-name-gtin'));
                    
                }
            } );
        }
    };
    
    wcmb_product_classify.init();


    var catWrapper = $( '.wcmb-product-categories-wrap' );
    var catParent = catWrapper.parent();
    var windowWidth = $(window).width();
    var scrollAmmount = 0;
    var addWidthToWrapper = 0;
    var columnItem, currentColumnNumber;
    var enableRightArrow = false;

    if( windowWidth >= 1100){
        columnItem = 3;
    } else if(windowWidth <= 1099 && windowWidth >= 640){
        columnItem = 2;
    } else{
        columnItem = 1;
        $('.initial-graphic').hide();
    }
     
    var get_cat_width = Math.floor(toFixed(100/columnItem, 2) * catWrapper.width()/100);
    var cat_column = catWrapper.find('.cat-column');
    cat_column.css('width', get_cat_width); 
    
    function toFixed(num, fixed) {
        fixed = fixed || 0;
        fixed = Math.pow(10, fixed);
        return Math.floor(num * fixed) / fixed;
    }

    var bodyDirection;
    if ($("body").hasClass("rtl")) {
        bodyDirection = 'right';
    } else{
        bodyDirection = 'left';
    }


    function checkCategoryScroller(){ 

        currentColumnNumber = catWrapper.find('.cat-column').last().attr('data-level'); 
 
        if(currentColumnNumber > columnItem){
            scrollAmmount = (currentColumnNumber - columnItem) * get_cat_width;
            addWidthToWrapper = catParent.width() + scrollAmmount;
            if(bodyDirection == 'left'){
                catWrapper.css(
                    {
                        'width': addWidthToWrapper, 
                        'margin-left': -scrollAmmount
                    }
                ); 
            } else{
                catWrapper.css(
                    {
                        'width': addWidthToWrapper, 
                        'margin-right': -scrollAmmount
                    }
                ); 
            }
        } else{
            if(bodyDirection == 'left'){
                catWrapper.css({'width': catParent.width(), 'margin-left': 0});
            } else{
                catWrapper.css({'width': catParent.width(), 'margin-right': 0});
            }
            
        }
         
    }    
    
    $('.cat-left-scroller').on('click', function(){
        if(scrollAmmount > 0){
            productCatLeftScroller();
        }
    });
    $('.cat-right-scroller').on('click', function(){
        if(enableRightArrow){
            productCatRightScroller();
        }
    });

    function productCatLeftScroller(){  
        scrollAmmount = scrollAmmount - get_cat_width;
        if(bodyDirection == 'left'){
            catWrapper.css({'margin-left': -scrollAmmount}); 
        } else{
            catWrapper.css({'margin-right': -scrollAmmount}); 
        }        
        enableRightArrow = true; 
    }
    function productCatRightScroller(){
        if( currentColumnNumber >= columnItem && (columnItem * get_cat_width < addWidthToWrapper - scrollAmmount - get_cat_width)){
            scrollAmmount = scrollAmmount + get_cat_width;
            if(bodyDirection == 'left'){
                catWrapper.css({'margin-left': -scrollAmmount}); 
            } else{
                catWrapper.css({'margin-right': -scrollAmmount}); 
            }
        }
    }
    
    // list a product by search gtin / product name
    $( '#search-list-a-product-btn' ).on( 'click', function() {
        var data = {
            action: 'wcmb_list_a_product_by_name_or_gtin',
            keyword: $('#search-list-a-product-key').val()
        };
        $.post(wcmb_product_classify_script_data.ajax_url, data, function (response) { 
            if(response.results){
                $('.wcmb-list-of-products-wrap').html(response.results);
            }else{
                $('.wcmb-list-of-products-wrap').html('');
            }
        });
    });
    
    // create duplicate product from list a product by search gtin / product name
    $('body').on('click', '.wcmb-list-of-products-wrap ul li a', function () {
        block($('.wcmb-list-of-products-wrap'));
        var product_id = $(this).data('product_id');
        var data = {
            action: 'wcmb_create_duplicate_product',
            product_id: product_id
        };
        $.post(wcmb_product_classify_script_data.ajax_url, data, function (response) {
            unblock($('.wcmb-list-of-products-wrap'));
            if (response.status) {
                window.location.href = response.redirect_url;
            }
        });
    });
    
    // list a product categorywise
    $( document.body ).on( 'click', '.wcmb-product-categories-wrap .list-group-item', function() { 
        block( $( '.wcmb-product-categories-wrap' ) );
        var $wrapper = $( this ).closest( '.wcmb-product-categories-wrap' );
        var $hierarchy = $( this ).parents().find( '.wcmb-product-category-hierarchy' );
        var $_this = $( this );
        var data = {
            action: 'wcmb_product_classify_next_level_list_categories',
            term_id: $_this.data('term-id'),
            taxonomy: $_this.data('taxonomy'),
            cat_level: $_this.parent().data('cat-level')
        };
        $.post(wcmb_product_classify_script_data.ajax_url, data, function (response) { 
            unblock( $( '.wcmb-product-categories-wrap' ) );
            if(response.hierarchy){
                $hierarchy.html(response.hierarchy);
            }
            if(response.html_level){
                $_this.parents().siblings( '.wcmb-pro-cat-level.'+response.level+'-level-cat' ).remove();
                $wrapper.append('<div class="wcmb-pro-cat-level '+response.level+'-level-cat col-md-4">'+ response.html_level +'</div>');
            }
            $_this.addClass('active').siblings().removeClass('active');
        });
    });
    
    // search category by term name
    $( '#search-by-term-btn' ).on( 'click', function() {
        var data = {
            action: 'wcmb_product_classify_search_category_level',
            keyword: $('#search-by-term-key').val()
        };
        $.post(wcmb_product_classify_script_data.ajax_url, data, function (response) { 
            if(response.results){
                $('.wcmb-product-category-search-result-wrap').html(response.results);
            }else{
                $('.wcmb-product-category-search-result-wrap').html('');
            }
        });
    });
 
})(jQuery);

