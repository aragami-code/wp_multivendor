jQuery( document ).ready( function ( $ ) {
    //checkbox_custome_design();
    $( ".hasmenu > a" ).click( function ( e ) {

        if ( $( this ).attr( 'href' ) == '#' )
            e.preventDefault();

        if ( !$( this ).hasClass( "active" ) ) {
            $( ".hasmenu" ).find( "a" ).removeClass( "active" );
            $( this ).addClass( "active" );
            if ( !$( this ).closest( "ul" ).hasClass( 'submenu' ) ) {
                $( ".hasmenu" ).find( "ul" ).slideUp();
            }
            $( this ).next( "ul" ).slideDown();
        } else {
            $( this ).removeClass( "active" );
            $( '.hasmenu' ).find( "ul" ).slideUp();
        }

    } );


    $( ".wcmb_stat_start_dt" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".wcmb_stat_end_dt" ).datepicker( "option", "minDate", selectedDate );
        }
    } );
    $( ".wcmb_stat_end_dt" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".wcmb_stat_start_dt" ).datepicker( "option", "maxDate", selectedDate );
        }
    } );
    $( ".wcmb_start_date_order" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".wcmb_end_date_order" ).datepicker( "option", "minDate", selectedDate );
        }
    } );
    $( ".wcmb_end_date_order" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".wcmb_start_date_order" ).datepicker( "option", "maxDate", selectedDate );
        }
    } );
    $( ".wcmb_tab" ).tabs();

    var sideslider = $( '[data-toggle=collapse-side]' );
    var sel = sideslider.attr( 'data-target' );
    var sel2 = sideslider.attr( 'data-target-2' );
    sideslider.click( function ( event ) {
        // $(this).toggleClass('collapsed');
        $( sel ).toggleClass( 'in' );
        $( sel2 ).toggleClass( 'out' );
        $( '.side-collapse-container' ).toggleClass( 'large' );

        if ( $( window ).width() < 768 ) {
            $( '#page-wrapper' ).toggleClass( 'overlay' );
        }
    } );

    // sidebar menu 
    sibdebarToggle();

    mapNavWrap();

    $( document ).on( 'change', '#wcmb_visitor_stats_date_filter', function () {
        mapNavWrap();
    } ); 

    // table responsive 
        
    $( ".responsive-table" ).each( function ( index ) {
        
        var getTh = $( this ).find( 'thead th' );
        var getTr = $( this ).find( 'tbody tr' );
        var getTd = $( this ).find( 'tbody td' );

        for ( var tr = 0; tr < getTr.length; tr++ ) {

            for ( var i = 0; i < getTh.length; i++ ) {
                $( getTd[i + tr * getTh.length] ).attr( 'data-th', $( getTh[i] ).html() );
            }

        }
    } ); 


    // Tool-tips
    $('.img_tip').each(function () {
        $(this).qtip({
            content: $(this).attr('data-desc'),
            position: {
                my: 'top center',
                at: 'bottom center',
                viewport: $(window)
            },
            show: {
                event: 'mouseover',
                solo: true,
            },
            hide: {
                inactive: 6000,
                fixed: true
            },
            style: {
                classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css',
                width: 200
            }
        });
    });

    // category scroller
    $(".wcmb-product-cat-level, .wcmbCustomScroller").mCustomScrollbar();



} );


var isMobile = {
    Android: function () {
        return navigator.userAgent.match( /Android/i );
    },
    BlackBerry: function () {
        return navigator.userAgent.match( /BlackBerry/i );
    },
    iOS: function () {
        return navigator.userAgent.match( /iPhone|iPad|iPod/i );
    },
    Opera: function () {
        return navigator.userAgent.match( /Opera Mini/i );
    },
    Windows: function () {
        return navigator.userAgent.match( /IEMobile/i );
    },
    any: function () {
        return ( isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows() );
    } };

function mapNavWrap() {
    jQuery( '.jqvmap-zoomin, .jqvmap-zoomout' ).wrapAll( '<div class="map-nav"></div>' );
}



jQuery( document ).ready( function ( $ ) {
    var window_width = $( window ).width();
    if ( window_width <= 640 ) {
        var active_menu = $( ".wcmb_main_menu ul li.hasmenu>a.active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.hide();
        active_menu.removeClass( 'active' );
        if ( !active_menu.hasClass( 'responsive_active' ) ) {
            active_menu.addClass( 'responsive_active' );
        }
    }
} );
jQuery( window ).resize( function () {
    var $ = jQuery.noConflict();
    var window_width = jQuery( window ).width();
    if ( window_width <= 640 ) {
        var active_menu = jQuery( ".wcmb_main_menu ul li.hasmenu>a.active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.hide();
        active_menu.removeClass( 'active' );
        if ( !active_menu.hasClass( 'responsive_active' ) ) {
            active_menu.addClass( 'responsive_active' );
        }
    } else {
        var active_menu = jQuery( ".wcmb_main_menu ul li.hasmenu>a.responsive_active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.show();
        active_menu.removeClass( 'responsive_active' );
        if ( !active_menu.hasClass( 'active' ) ) {
            active_menu.addClass( 'active' );
        }
    }

    // sidebar menu
    jQuery( function ( $ ) {
        if ( !isMobile.any() ) {
            sibdebarToggle();
        }
    } );


} );

// sidebar menu
function sibdebarToggle() {
    jQuery( '#page-wrapper' ).on( 'click', function ( event ) {
        if ( !jQuery( '.navbar-default.sidebar' ).hasClass( 'in' ) && jQuery( window ).width() <= 768 ) {
            jQuery( '.side-collapse-container' ).toggleClass( 'large' );
            jQuery( '.navbar-default.sidebar' ).addClass( 'in' );
            jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        }
    } );

    if ( jQuery( window ).width() <= 768 ) {
        jQuery( '.sidebar' ).addClass( 'in' );
        jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        jQuery( '.side-collapse-container' ).removeClass( 'large' );
    } else {
        jQuery( '.sidebar' ).removeClass( 'in' );
        jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        jQuery( '.side-collapse-container' ).removeClass( 'large' );
    }
}


function toggleAllCheckBox( self, tableId ) {
    if ( jQuery( self ).is( ':checked' ) ) {
        jQuery( '#' + tableId ).find( 'tbody tr td input[type=checkbox]' ).not( ":disabled" ).prop( 'checked', true );
    } else {
        jQuery( '#' + tableId ).find( 'tbody tr td input[type=checkbox]' ).prop( 'checked', false );
    }
}
