jQuery(document).ready(function($) {
    //first check if the form is present
    var $tab_length = $('#wp_feedback_tabs li').length;

    if(0 == $tab_length)
        return;

    //make the tab
    var $disabled = new Array();
    for(var i = 0; i < $tab_length; i++) {
        $disabled.push(i);
    }
    //alert($disabled);
    var $tab_selected = 0;
    var $tabs = $('#wp_feedback_tabs_wrap').tabs({
        selected : $tab_selected,
        disabled : $disabled,
        fx : {height : 'toggle', opacity : 'toggle'},
        show : function() {
            $(this).tabs('option', 'disabled', $disabled);
        }
    });

    //validation
    //alert(typeof($.validationEngine));
    $('#feedback_form').validationEngine();


    //assign the navigation and submit button
    //alert($('#wp_feedback .navigation .prev_button').length);
    var $prev = $('#wp_feedback .wp_feedback_navigation .prev_button').button({
        disabled : true
    });

    var $next = $('#wp_feedback .wp_feedback_navigation .next_button').button({
        disabled : ($tab_length == 1 ? true : false)
    });

    var $submit = $('#wp_feedback .wp_feedback_navigation .sub_button').button({
        disabled : ($tab_length == 1 ? false : true)
    });

    $prev.click(function(e) {
        e.preventDefault();
        $tab_selected = $tabs.tabs('option', 'selected');

        if(0 != $tab_selected && $($('#wp_feedback_tabs li').eq($tab_selected).find('a').attr('href')).validationEngine('validate')) {
            $tabs.tabs('option', 'disabled', []);
            $tabs.tabs('option', 'selected', $tab_selected - 1);
            $next.button('option', 'disabled', false);

            if($tab_selected - 1 == 0) {
                $prev.button('option', 'disabled', true);
            }
            $submit.button('option', 'disabled', true);
            $('html,body').animate({scrollTop:$('#wp_feedback').offset().top}, 200);
        }
    });

    $next.click(function(e) {
        e.preventDefault();
        $tab_selected = $tabs.tabs('option', 'selected');

        if($tab_length - 1 != $tab_selected && $($('#wp_feedback_tabs li').eq($tab_selected).find('a').attr('href')).validationEngine('validate')) {
            $tabs.tabs('option', 'disabled', []);
            $tabs.tabs('option', 'selected', $tab_selected + 1);
            $prev.button('option', 'disabled', false);

            if($tab_selected + 1 == $tab_length - 1) {
                $next.button('option', 'disabled', true);
                $submit.button('option', 'disabled', false);
            }
            $('html,body').animate({scrollTop:$('#wp_feedback').offset().top}, 200);
        }
    });

    if($('.toggle_f input[type="checkbox"]').length) {

        $('.toggle_f input[type="checkbox"]').each(function() {
            if($(this).attr('checked')) {
                $(this).parent().parent().siblings('div.toggle_d').show();
            } else {
                $(this).parent().parent().siblings('div.toggle_d').hide();
            }
        })

        $('.toggle_f input[type="checkbox"]').change(function() {
            var target = $(this).parent().parent().siblings('div.toggle_d');
            if(this.checked) {
                target.stop(true, true).animate({opacity: 'toggle', height: 'toggle'}, 'fast');
            } else {
                target.stop(true, true).animate({opacity: 'toggle', height: 'toggle'}, 'normal');
            }
        })
    }

    //ajax submit
    $('#feedback_form').submit(function(e) {
        e.preventDefault();
        if(!$('#feedback_form').validationEngine('validate')) {
            return false;
        }
        $('html,body').animate({scrollTop:$('#wp_feedback').offset().top - 100}, 200);
        var data = $('#feedback_form').serialize();

        $('#feedback_form').hide('fast', function() {
            $('#wp_feedback_ajax').show('fast', function() {
                $.post(wpFBObj.ajaxurl, data, function(obj) {
                    //alert(obj.msg);
                    $('#wp_feedback_ajax').hide('fast', function() {
                        $('#wp_feedback').append(obj.msg).hide().show('normal');
                    });
                    if(obj.type == 'fail') {
                        $('#feedback_form').show('normal');
                    }

                }, 'json');
            });
        });
    })
});
