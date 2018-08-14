/**
 * @file
 * jQuery code.
 * Based on code: Adrian "yEnS" Mato Gondelle, twitter: @adrianmg
 * Modifications for Drupal: Grzegorz Bartman grzegorz.bartman@openbit.pl
 */


// Setting up popup.
// 0 means disabled; 1 means enabled.
var popupStatus = 0;

/**
 * Loading popup with jQuery.
 */
function popup_message_load_popup() {
  // Loads popup only if it is disabled.
  if (popupStatus === 0) {
    jQuery("#popup-message-background").css({
      "opacity": "0.7",
    });    
    jQuery("#popup-message-background").fadeIn("slow");
    jQuery("#page").toggleClass("blur");
    jQuery("#popup-message-window").fadeIn("slow");
    popupStatus = 1;
  }
}

/**
 * Disabling popup with jQuery.
 */
function popup_message_disable_popup() {
  // Disables popup only if it is enabled.
  if (popupStatus == 1) {
    jQuery("#popup-message-background").fadeOut("slow").remove();
    jQuery("#popup-message-window").fadeOut("slow").remove();
    jQuery("#page").toggleClass("blur");
    popupStatus = 0;
  }
}

/**
 * Centering popup.
 */
function popup_message_center_popup(width, height) {
  // Request data for centering.
  var windowWidth = document.documentElement.clientWidth;
  var windowHeight = document.documentElement.clientHeight;

  var popupWidth = 0;
  if (typeof width == "undefined") {
    popupWidth = jQuery("#popup-message-window").width();
  }
  else {
    popupWidth = width;
  }
  var popupHeight = 0;
  if (typeof height == "undefined") {
    popupHeight = jQuery("#popup-message-window").height();
  }
  else {
    popupHeight = height;
  }

  // Centering.
  jQuery("#popup-message-window").css({
    "width" : popupWidth + "px",
    "height" : popupHeight + "px",
    "top": windowHeight / 2 - popupHeight / 2,
    "left": windowWidth / 2 - popupWidth / 2
  });
  // Only need force for IE6.
  jQuery("#popup-message-background").css({
    "height": windowHeight
  });

}

/**
 * Display popup message.
 */
function popup_message_display_popup(popup_message_title, popup_message_body, width, height, close) {
  if (typeof close == "undefined") close = true;
  
  var close_markup = '';
  if ( close ) {
    close_markup = "<a id='popup-message-close'>x</a>";
  }
  if (!jQuery("div").is("#popup-message-window")) {
      jQuery('body').append("<div id='popup-message-window'>"
          + close_markup
          + "<div class='popup-message-title'>"
          +     popup_message_title
          + "</div>"
          + "<div id='popup-message-content'>"
          +     popup_message_body
          + "</div></div>"
          + "<div id='popup-message-background'></div>");
  }

  // Loading popup.
  popup_message_center_popup(width, height);
  popup_message_load_popup();

  Drupal.attachBehaviors('#popup-message-window');

  // Closing popup.
  // Click the x event!
  if ( close ) {
    jQuery("#popup-message-close").click(function() {
      popup_message_disable_popup();
    });
    // Click out event!
    jQuery("#popup-message-background").click(function() {
      popup_message_disable_popup();
    });
    // Press Escape event!
    jQuery(document).keypress(function(e) {
      if (e.keyCode == 27 && popupStatus == 1) {
        popup_message_disable_popup();
      }
    });
  }
}

/**
 * Helper function for get last element from object.
 * Used if on page is loaded more than one message.
 */
function popup_message_get_last_object_item(variable_data) {
  if (typeof(variable_data) == 'object') {
      variable_data = variable_data[(variable_data.length - 1)];
  }
  return variable_data;
}

Drupal.behaviors.popup_message = {
  attach: function(context) {
    var timestamp = (+new Date());
    var check_cookie = Drupal.settings.popup_message.check_cookie;
    check_cookie = popup_message_get_last_object_item(check_cookie);
    var popup_message_cookie = jQuery.cookie("popup_message_displayed"),
    delay = Drupal.settings.popup_message.delay,
    show_popup = false;

    if ((delay >=0)||(typeof delay == "undefined")) {
        delay = delay * 1000;
        if (!popup_message_cookie || check_cookie == 0) {
            // Set cookie.
            jQuery.cookie("popup_message_displayed", timestamp, {path: '/'});
            // Display message.
            show_popup = true;
        }
        else {
            popup_message_cookie = parseInt(popup_message_cookie, 10);
            show_popup = timestamp < popup_message_cookie + delay;
        }

        if (show_popup) {
            var run_popup = function () {
                // Get variables.
                var popup_message_title = Drupal.settings.popup_message.title,
                    popup_message_body = Drupal.settings.popup_message.body,
                    popup_message_width = Drupal.settings.popup_message.width,
                    popup_message_height = Drupal.settings.popup_message.height;
                    popup_message_close = Drupal.settings.popup_message.close;

                popup_message_title = popup_message_get_last_object_item(popup_message_title);
                popup_message_body = popup_message_get_last_object_item(popup_message_body);
                popup_message_width = popup_message_get_last_object_item(popup_message_width);
                popup_message_height = popup_message_get_last_object_item(popup_message_height);
                popup_message_close = popup_message_get_last_object_item(popup_message_close);
                popup_message_display_popup(
                    popup_message_title,
                    popup_message_body,
                    popup_message_width,
                    popup_message_height,
                    popup_message_close);
            };

            var trigger_time = delay;
            setTimeout(run_popup, trigger_time);
        }
    }
  }
};


// функция popup_message для вызова из ajax
(function($) {
    // два метода для функции popup_message: show и hide
    var methods = {
        show : function( ) {
            var popup_message_title = Drupal.settings.popup_message.title,
                popup_message_body = Drupal.settings.popup_message.body,
                popup_message_width = Drupal.settings.popup_message.width,
                popup_message_height = Drupal.settings.popup_message.height;
                popup_message_close = Drupal.settings.popup_message.close;

                popup_message_title = popup_message_get_last_object_item(popup_message_title);
                popup_message_body = popup_message_get_last_object_item(popup_message_body);
                popup_message_width = popup_message_get_last_object_item(popup_message_width);
                popup_message_height = popup_message_get_last_object_item(popup_message_height);
                popup_message_close = popup_message_get_last_object_item(popup_message_close);
                popup_message_display_popup( popup_message_title, popup_message_body, popup_message_width, popup_message_height, popup_message_close);
        },
        hide : function( ) {
            popup_message_disable_popup();
        }
    };

    //
    $.fn.popup_message = function( method ) {
        // логика вызова метода
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else {
            $.error( 'Метод с именем ' +  method + ' не существует для jQuery.popup_message' );
        }
    }
})(jQuery);