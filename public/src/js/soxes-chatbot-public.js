(function ($) {
    'use strict';

    var ajax_running = false;

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    var ChatbotActions = function () {
        let parent_func = this;
        /**
         * Trigger open the chatbot full screen
         *
         **/
        parent_func.open_chatbot = function () {
            const $target = $('.soxes_overlay_container.chatbot_bottom');
            if ($target.hasClass('bot_active')) {
                return false;
            }
            $('html, body').addClass('hide-scrollbar');
            $target.addClass('bot_active').css({
                right: "0"
            });
            $target.removeClass('bot_hidden');
            $('body').addClass('bot_active');
            //Move icon
            $target.fadeToggle(200);
            $target.find('.close-text').fadeToggle(100);
            $('.bot_icon_only').fadeToggle("slow");
        }

        /**
         * Hide the chatbot
         *
         **/
        parent_func.close_chatbot = function () {
            const $target = $('.soxes_overlay_container.chatbot_bottom');
            if (!$target.hasClass('bot_active')) {
                return false;
            }
            $('html, body').removeClass('hide-scrollbar');
            $target.fadeToggle('fast');
            $target.find('.close-text').fadeToggle(100);
            setTimeout(function () {
                $target.removeClass('bot_active');
                $('body').removeClass('bot_active');
            }, 200);

            $('.bot_icon_only').removeClass('bot_active').animate({
                right: "10px",
                bottom: "110px"
            }).fadeToggle("fast");
        }

        /**
         * Hide the chatbot with animation
         *
         **/
        parent_func.close_chatbot_animation = function () {
            if ($('.soxes_overlay_container.chatbot_bottom').hasClass('bot_active')) {
                parent_func.close_chatbot();
                setTimeout(function () {
                    if (soxes_chatbot.chatbot_type && soxes_chatbot.chatbot_type === 'Shortcode') {
                        $('.soxes_overlay_container.chatbot_bottom').css({'right': '-400px'});
                    }
                }, 200);
            } else {
                setTimeout(function () {
                    $('.soxes_overlay_container.chatbot_bottom').css({'right': '-400px'});
                    $('.bot_icon_only').removeClass('hidden').animate({
                        right: "10px"
                    });
                }, 200);
            }
            parent_func.hide_all_answers();
        }
        /**
         * When user close a choosing, remove all questions below from it
         *
         **/
        parent_func.hide_all_answers = function () {
            const $target = $('.soxes_overlay_container.chatbot_bottom .bot_content');
            setTimeout(function () {
                $target.find('.wrap_answer').removeClass('active clicked');
                $target.not(':first').remove();
                $('.soxes_overlay_container.chatbot_bottom .bot_content:first-child').find('.wrap_answer').addClass('active');
            }, 200);
        }

        /**
         * When user choose an answer, hide other answers
         *
         **/
        parent_func.hide_other_answers = function (parent, element) {
            element.find('.wrap_answer').removeClass('active clicked');
            parent.addClass('active clicked');
        }

        /**
         * When user close a choosing, show other answers in the same question
         *
         **/
        parent_func.show_other_answers = function (parent, element) {
            element.find('.wrap_answer').addClass('active');
            parent.removeClass('clicked');
            element.nextAll('.bot_content').remove();
        }

        /**
         * render question & answer
         *
         **/
        parent_func.render_question = function (data) {
            let $html = '',
                first = true;
            $html += '<div class="bot_content">';
            $html += '<div class="bot_icon"><img src="' + soxes_chatbot.icon_chatbot + '" alt="chatbot icon"/></div>';
            $html += '<div class="bot_item_wrapper">';
            if (data.soxes_question !== '') {
                $html += '<div class="bot_item bot_question bot_visible">';
                $html += data.soxes_question;
                $html += '</div>';
                if (data.soxes_answers !== '') {
                    $html += '<div class="bot_item bot_answers bot_visible">';
                    for (let j in data.soxes_answers) {
                        let $close_icon = (typeof soxes_chatbot.chatbot_icon != 'undefined') ? soxes_chatbot.chatbot_icon : '<svg viewBox="0 0 16 16" role="presentation"><use xlink:href="#svgicon-close"></use></svg>';
                        switch (data.soxes_answers[j].soxes_select_type) {
                            case 'Form':
                                if (typeof data.soxes_answers[j].soxes_answer !== 'undefined' && data.soxes_answers[j].soxes_answer != '' && data.soxes_answers[j].soxes_answer_form != '') {
                                    $html += '<div class="wrap_answer active wrap_answer_form">';
                                    $html += '<button class="bot__item bot__answer bot_open_form" data-value="' + data.soxes_answers[j].soxes_answer_form + '" data-action="openForm">' + data.soxes_answers[j].soxes_answer + '</button>';
                                    $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                    $html += '</div>';
                                    if (first) {
                                        parent_func.enhance_form(data.soxes_answers[j].soxes_answer_form, first);
                                        first = false;
                                    }
                                }
                                break;
                            case 'Direct contact':
                                let $postion = (data.soxes_answers[j].soxes_direct_contact.position) ? data.soxes_answers[j].soxes_direct_contact.position : '',
                                    $name = (data.soxes_answers[j].soxes_direct_contact.display_name) ? data.soxes_answers[j].soxes_direct_contact.display_name : '',
                                    $gender = (data.soxes_answers[j].soxes_direct_contact.gender) ? data.soxes_answers[j].soxes_direct_contact.gender : '',
                                    $direct_text = (typeof soxes_chatbot.direct_txt != 'undefined') ? soxes_chatbot.direct_txt : '',
                                    $address_txt = (typeof soxes_chatbot.address_txt != 'undefined') ? soxes_chatbot.address_txt : '',
                                    $email = (typeof data.soxes_answers[j].soxes_direct_contact.email != 'undefined') ? data.soxes_answers[j].soxes_direct_contact.email : '';
                                //Generate email
                                let part2 = Math.pow(2, 6),
                                    part3 = String.fromCharCode(part2),
                                    part4 = 'soxes.ch',
                                    part5 = $email + String.fromCharCode(part2) + part4,
                                    final_mail = "<span class='email'>" + $email + part3 + part4 + "</span>";
                                if (typeof data.soxes_answers[j].soxes_answer !== 'undefined' && data.soxes_answers[j].soxes_answer != '' && data.soxes_answers[j].soxes_direct_contact.ID != '') {
                                    $html += '<div class="wrap_answer active wrap_answer_form">';
                                    $html += '<button class="bot__item bot__answer bot_open_form bot_open_form_direct" data-value="' + data.soxes_answers[j].soxes_answer_form + '" data-email="' + $email + '" data-action="openForm">' + data.soxes_answers[j].soxes_answer + '</button>';
                                    if ($email) {
                                        $html += '<div class="author-wrapper">';
                                        $html += '<div class="contact-info">';
                                        $html += '<div class="avatar">';
                                        if (typeof data.soxes_answers[j].soxes_direct_contact.avatar.sizes.medium != 'undefined') {
                                            $html += '<img src="' + data.soxes_answers[j].soxes_direct_contact.avatar.sizes.medium + '" alt="' + $name + '" />';
                                        }
                                        $html += '</div>';
                                        $html += '<div class="author-details">';
                                        $html += '<p><strong>' + $gender + '</strong></p>';
                                        $html += '<p class="info"><strong class="name">' + $name + '</strong><br/><span class="position">' + $postion + '</span><br/><br/>\n' +
                                            '                <strong class="company">' + $address_txt + '</strong><br/>' + final_mail + '</p>';
                                        $html += '</div>';
                                        $html += '</div>';
                                        $html += '</div>';
                                    }
                                    $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                    $html += '</div>';
                                    if (first) {
                                        parent_func.enhance_form(data.soxes_answers[j].soxes_answer_form, first);
                                        first = false;
                                    }
                                }
                                break;
                            case 'Link':
                                if (typeof data.soxes_answers[j].soxes_answer !== 'undefined' && data.soxes_answers[j].soxes_answer != '' && data.soxes_answers[j].soxes_answer_link != '') {
                                    $html += '<div class="wrap_answer_outer">';
                                    $html += '<div class="wrap_answer_inner">';
                                    $html += '<div class="tooltip_chatbot">' + soxes_chatbot.tooltip_text + '</div>';
                                    $html += '<div class="wrap_answer active">';
                                    $html += '<a class="bot__item bot__answer bot_open_link" href="' + data.soxes_answers[j].soxes_answer_link + '">' + data.soxes_answers[j].soxes_answer + '</a>';
                                    $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                    $html += '</div>';
                                    $html += '</div>';
                                    $html += '</div>';
                                }
                                break;
                            case 'Category':
                                if (data.soxes_answers[j].soxes_category != '' && $.isArray(data.soxes_answers[j].soxes_category)) {
                                    //Keep the same bubble
                                    for (var x in data.soxes_answers[j].soxes_category) {
                                        $html += '<div class="wrap_answer_outer">';
                                        $html += '<div class="wrap_answer_inner">';
                                        $html += '<div class="tooltip_chatbot">' + soxes_chatbot.tooltip_text + '</div>';
                                        $html += '<div class="wrap_answer active">';
                                        $html += '<a class="bot__item bot__answer bot_open_link" href="' + data.soxes_answers[j].soxes_category[x].link + '">' + data.soxes_answers[j].soxes_category[x].name + '</a>';
                                        $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                        $html += '</div>';
                                        $html += '</div>';
                                        $html += '</div>';
                                    }
                                }
                                break;
                            case 'Posts':
                                if (data.soxes_answers[j].soxes_answer_posts != '' && $.isArray(data.soxes_answers[j].soxes_answer_posts)) {
                                    //Keep the same bubble
                                    for (var x in data.soxes_answers[j].soxes_answer_posts) {
                                        $html += '<div class="wrap_answer_outer">';
                                        $html += '<div class="wrap_answer_inner">';
                                        $html += '<div class="tooltip_chatbot">' + soxes_chatbot.tooltip_text + '</div>';
                                        $html += '<div class="wrap_answer active">';
                                        $html += '<a class="bot__item bot__answer bot_open_link" target="_blank" href="' + data.soxes_answers[j].soxes_answer_posts[x].link + '">' + data.soxes_answers[j].soxes_answer_posts[x].name + '</a>';
                                        $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                        $html += '</div>';
                                        $html += '</div>';
                                        $html += '</div>';
                                    }
                                }
                                break;
                            default:
                                if (typeof data.soxes_answers[j].soxes_answer !== 'undefined' && data.soxes_answers[j].soxes_answer != '') {
                                    $html += '<div class="wrap_answer active">';
                                    $html += '<button class="bot__item bot__answer" data-value="' + data.soxes_answers[j].id + '" data-action="openQuestion">' + data.soxes_answers[j].soxes_answer + '</button>';
                                    $html += '<span class="sprite sprite--close">' + $close_icon + '</span>';
                                    $html += '</div>';
                                }
                                break;
                        }
                    }
                    $html += '</div>';
                }
            }
            $html += '</div>';
            $html += '</div>';
            $('.soxes_overlay_container.chatbot_bottom .bot_inner').append($html).show('slow');
        }

        /**
         * for fixed bottom type only, show it as a pop-out fixed in bottom of the screen
         *
         **/
        parent_func.open_modal = function (target, text, email = '', header = '') {
            localStorage.setItem('text_chatbot', text);
            localStorage.setItem('email_chatbot', email);
            if (header) {
                target.find('.modal-header').append(header);
            }
            if (text || email) {
                parent_func.insert_text(target, text, email);
            }
            target.addClass('show').fadeToggle("slow", "linear");
            if ($('.grecaptcha-badge').length > 0) {
                $('.grecaptcha-badge').css({'opacity': 1, 'z-index': '99999'});
            }
        }

        /**
         * for fixed bottom type only, hide it out of screen
         *
         **/
        parent_func.close_modal = function (target) {
            let $textarea = target.find('textarea');
            $textarea.val('');
            target.find('.contact-info').remove();
            target.removeClass('show').fadeToggle("fast", "linear");
            target.find('.gform_validation_errors').remove();
            target.find('.gfield_validation_message').remove();
            target.find('.gfield').removeClass('sk-gfield_error');
            if (!target.hasClass('clicked')) {
                target.addClass('clicked');
            }
            if ($('.grecaptcha-badge').length > 0) {
                $('.grecaptcha-badge').css({'opacity': 0});
            }
            localStorage.removeItem('text_chatbot');
            localStorage.removeItem('email_chatbot');
        }

        /**
         * after user click on bubble to open contact form
         *
         **/
        parent_func.insert_text = function (target, text = '', email = '') {
            let $textarea = target.find('.ginput_container_textarea textarea');
            let e_text = (typeof text != 'undefined' && text.length > 0) ? text : localStorage.getItem('text_chatbot'),
                e_email = (typeof email != 'undefined' && email.length > 0) ? email : localStorage.getItem('email_chatbot');
            if (typeof e_text == 'string' && e_text.length > 0) {
                let clicked_text = soxes_chatbot.hello_txt + ',\n\n' + e_text.replace(/(<([^>]+)>)/gi, "") + " ";
                $textarea.val(clicked_text).trigger('click');
                if (!$textarea.closest('.gfield').hasClass('input-focused')) {
                    $textarea.closest('.gfield').addClass('input-focused text-gray');
                }
            }
            if (e_email) {
                target.find('input[name="input_7"]').val(e_email);
            }
        }

        /**
         * render GF form by AJAX
         *
         **/
        parent_func.render_form = function (form_id) {
            let form_target = $('.modal-form-chatbot #soxes-form-wrapper_' + form_id);
            form_target.closest('.modal-form-chatbot').addClass('loading');
            if (!ajax_running) {
                $.ajax({
                    url: soxes_chatbot.ajax_url,
                    type: 'GET',
                    data: {
                        action: 'rerender_form',
                        form_id: form_id ? form_id : 0
                    },
                    beforeSend: function () {
                        ajax_running = true;
                    },
                    complete: function () {
                        ajax_running = false;
                    },
                    success: function (res) {
                        form_target.html(res).addClass('gf-after-ajax-call');
                        setTimeout(function () {
                            form_target.closest('.modal-form-chatbot').removeClass('loading');
                            form_target.closest('.modal-wrapper').removeClass('confirmation');
                            form_target.find('input:not(.gfield-choice-input, .gform_hidden, [type=hidden]), textarea').after('<div class="chatbot-input-mask"><span>&nbsp;</span></div>');
                            form_target.find('.phone_number_contact input').removeAttr("disabled");
                            form_target.find('.phone_number_contact .gfield_required').hide();
                            parent_func.insert_text(form_target);
                        }, 500);
                        setTimeout(function () {
                            form_target.closest('.modal-wrapper').removeClass('clicked');
                        }, 5000);

                    }
                });
            }
        }

        /**
         * When user close a choosing, show other answers in the same question
         *
         **/
        parent_func.enhance_form = function (form_id, first = false) {
            if ($('.modal-form-chatbot #soxes-form-wrapper_' + form_id + ' .gform_wrapper').length == 0 && first) {
                parent_func.render_form(form_id);
            }
        }
    };


    setTimeout(function (e) {
        if ($('.soxes_overlay_container').length > 0) {
            var Chatbot = new ChatbotActions();
            let $default = $('.soxes_overlay_container.chatbot_bottom .default_questions').html(),
                $questions_before = [],
                $questions_after = [],
                $next_question = [];

            $('.modal-form-chatbot').find('.gform_wrapper').show('slow');
            //Exist default data
            if (typeof $default != 'undefined') {
                $questions_before = $.parseJSON($default);
            }
            //Check it is object or array
            if (typeof $questions_before.isArray === 'undefined') {
                for (let i in $questions_before)
                    $questions_after.push([i, $questions_before[i]]);
            } else {
                $questions_after = $questions_before;
            }
            //On click the answer
            $('body').on('click', '.bot_icon_only', function (e) {
                e.preventDefault();
                if (soxes_chatbot.chatbot_type && soxes_chatbot.chatbot_type === 'Fixed bottom') {
                    if (!$('.bot_icon_only').hasClass('hidden')) {
                        $('.soxes_overlay_container.chatbot_bottom').css({'right': '0'});
                    }
                } else {
                    Chatbot.open_chatbot();
                }
            });
            $('body').on('click', '.wrap_answer', function (e) {
                e.preventDefault();
                let $this = $(this),
                    $item = $this.find('.bot__item'),
                    $value = 0,
                    $add = true,
                    $real_answer;
                if ($item.hasClass('bot_open_link')) {
                    window.open($item.attr('href'));
                    $add = false;
                } else if ($item.hasClass('bot_open_form') && typeof $item.data('value') != 'undefined') {
                    let form = $item.data('value'),
                        text = ($item.data('text') == 'empty') ? '' : $item.html();
                    Chatbot.enhance_form(form, true);
                    if ($item.hasClass('bot_open_form_direct')) {
                        Chatbot.open_modal($('#modal-form-' + form), text, $item.data('email'), $item.parent().find('.author-wrapper').html());
                    } else {
                        Chatbot.open_modal($('#modal-form-' + form), text);
                    }
                    $add = false;
                } else {
                    $value = $item.data('value');
                }
                if ($item.data('match')) {
                    $real_answer = $('.soxes_overlay_container.chatbot_bottom').find("[data-match='" + $item.data('match') + "']").parent();
                } else {
                    $real_answer = $this;
                }
                //Open other answers
                if (!$this.hasClass('active clicked') && $add) {
                    //Popout whole screen
                    Chatbot.open_chatbot();
                    //Hide other answers
                    Chatbot.hide_other_answers($real_answer, $real_answer.closest('.bot_content'));

                    //Get next question data
                    if (typeof $value !== 'undefined' && $value !== 0 && $.isArray($questions_after)) {
                        $next_question = $.grep($questions_after, function (e) {
                            return e[1].soxes_parent_answer_id === $value;
                        });
                    }

                    //If next question is exist, render it
                    if (typeof $next_question[0] !== 'undefined' && typeof $next_question[0][1] !== 'undefined') {
                        var $data = $next_question[0][1];
                        Chatbot.render_question($data);
                    }
                }
                //Remove current answer
                else {
                    Chatbot.show_other_answers($real_answer, $real_answer.closest('.bot_content'));
                }
            });
            $('body').on('click', '.box-actions .sprite', function (e) {
                e.preventDefault();
                Chatbot.close_chatbot_animation();
            });
            $('body').on('focus', '.soxes-form-wrapper input, .soxes-form-wrapper textarea', function () {
                let gfield = $(this).closest('.gfield');
                if (!gfield.hasClass('input-focused')) {
                    gfield.addClass('input-focused');
                }
                if (gfield.hasClass('text-gray')) {
                    gfield.removeClass('text-gray');
                }
            });
            $('body').on('blur', '.soxes-form-wrapper input, .soxes-form-wrapper textarea', function () {
                let gfield = $(this).closest('.gfield');
                if (gfield.hasClass('input-focused') && $(this).val() == '') {
                    gfield.removeClass('input-focused');
                }
                if (!gfield.hasClass('text-gray')) {
                    gfield.addClass('text-gray');
                }
            });
            let $screen = $(window).width();
            //process autocomplete suggest
            if ($screen > 1024) {
                $('body').on('click', '.soxes-form-wrapper .chatbot-radio label', function () {
                    let form = $(this).closest('form');
                    //change value chose
                    form.find('.textarea-contact-content textarea').val(form.find('.textarea-contact-content textarea').val() + $(this).html() + ' ');
                    //change status div include
                    form.find('.textarea-contact-content .ginput_container .gfield_label').addClass('mdc-floating-label--float-above');
                });
                //Fix double click for mobile
            } else {
                $('body').on('click', '.soxes-form-wrapper .chatbot-radio .gchoice', function (e) {
                    e.preventDefault();
                    let form = $(this).closest('form');
                    //change value chose
                    form.find('.textarea-contact-content textarea').val(form.find('.textarea-contact-content textarea').val() + $(this).find('label').html() + ' ');
                    //change status div include
                    form.find('.textarea-contact-content .ginput_container .gfield_label').addClass('mdc-floating-label--float-above');
                });
            }
            $('body').on('click', '.soxes-form-wrapper .chatbot-radio .gchoice', function () {
                let gtextarea = $('.soxes-form-wrapper .textarea-contact-content');
                if (!gtextarea.hasClass('input-focused')) {
                    gtextarea.addClass('input-focused');
                }
                if (!gtextarea.hasClass('text-gray')) {
                    gtextarea.addClass('text-gray');
                }
            });
            $('body').on('keyup', '.soxes-form-wrapper .phone_number_contact input', function () {
                let phone = $(this).val().replace(/[^0-9+,\(\)\-]/g, '').substring(0, 20);
                $(this).val(phone);
                $('.soxes-form-wrapper .phone_number_contact input').val(phone);
            });
            $('body').on('click', '.modal-actions .sprite--close', function (e) {
                e.preventDefault();
                let modal = $(this).closest('.modal-wrapper');
                Chatbot.close_modal(modal);
                if ($('.modal-form-chatbot .gform_confirmation_wrapper').length > 0) {
                    let form_wrapper = modal.find('.soxes-form-wrapper').attr('id');
                    if (form_wrapper) {
                        let split_form = form_wrapper.split('soxes-form-wrapper_');
                        if (typeof split_form[1] != 'undefined') {
                            Chatbot.render_form(split_form[1]);
                        }
                    }
                }
            });
            $(document).on('keyup', function (e) {
                if (e.key == "Escape") {
                    Chatbot.close_chatbot_animation();
                }
            });
            $(document).on('gform_post_render', function (event, form_id, current_page) {
                if (soxes_chatbot.default_form == form_id) {
                    $('.soxes-form-wrapper input[type="text"], .soxes-form-wrapper input[type="email"], .soxes-form-wrapper input[type="tel"], .soxes-form-wrapper .ginput_container_textarea textarea').each(function () {
                        let gfield = $(this).closest('.gfield');
                        if ($(this).val().length > 0) {
                            gfield.addClass('input-focused input-focused text-gray');
                        }
                    });
                    soxes_change_required_status(form_id);
                    let $phone = $("#input_" + form_id + "_8").val();
                    setTimeout(function () {
                        $("#input_" + form_id + "_15").val($phone);
                    }, 100);
                }
            });
        }
    }, 400);

    $(document).ready(function () {
        setInterval(function () {
            chatbot_animation();
        }, 8000);
        setTimeout(function () {
            chatbot_animation();
        }, 2000);
    });

    function chatbot_animation() {
        if(!$('.chatbot-txt-1').hasClass('rotateInUpRight')) {
            $('.chatbot-txt-1').addClass('rotateInUpRight');
            $('.chatbot-txt-1').removeClass('rotateOutUpRight');
            $('.chatbot-txt-1').removeClass('hidden');
        }
        setTimeout(function(){
            if(!$('.chatbot-txt-1').hasClass('rotateOutUpRight')) {
                $('.chatbot-txt-1').addClass('rotateOutUpRight');
                $('.chatbot-txt-1').removeClass('rotateInUpRight');
            }
        }, 2000);
        setTimeout(function(){
            if(!$('.chatbot-txt-2').hasClass('rotateInUpRight')) {
                $('.chatbot-txt-2').removeClass('hidden');
                $('.chatbot-txt-2').addClass('rotateInUpRight');
                $('.chatbot-txt-2').removeClass('rotateOutDownRight');
                $('.chatbot-txt-1').addClass('hidden');
            }
        }, 2100);
        setTimeout(function(){
            if(!$('.chatbot-txt-2').hasClass('rotateOutDownRight')) {
                $('.chatbot-txt-2').addClass('rotateOutDownRight');
                $('.chatbot-txt-2').removeClass('rotateInUpRight');
            }
        }, 4100);
        setTimeout(function(){
            $('.chatbot-txt-2').addClass('hidden');
        }, 4600);
    }

    function soxes_change_required_status($form_id) {
        let form = $form_id ? $('#gform_' + $form_id) : $('.gform_body');
        if (form.find('.radio-inline input:checked').val() === 'by_phone') {
            form.find('.phone_number_contact .gfield_required').show();
        } else {
            form.find('.phone_number_contact .gfield_required').hide();
        }
        $('body').on('submit', '#gform_' + soxes_chatbot.default_form, function () {
            $('#gform_submit_button_' + soxes_chatbot.default_form).addClass('disabled');
        });
        $(document).on('change', '.radio-inline input[type=radio]', function () {
            let form = $(this).closest('form');
            if ($(this).val() === 'by_phone') {
                form.find('.phone_number_contact .gfield_required').show();
            } else if ($(this).val() === 'by_email') {
                form.find('.phone_number_contact .gfield_required').hide();
            }
        });
    }

})(jQuery);
