
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
const Swal = require('sweetalert2');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

/*
const app = new Vue({
    el: '#app'
});
*/

function setup_confirm_dialog() {
    $('input.confirm').click(function(event) {
        var form = $(this).parent('form');
        event.preventDefault();

        Swal({
            animation: false,
            title: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        })        
    });
}

function set_time_zone() {
    var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    var tzElement = $('select[name=timezone]');
    
    if(tzElement.val() == 'GMT') {
        tzElement.val(timeZone);
    }
}

function setup_edit_dialog() {
    var circle_uuid = $('div.comments').attr('data-circle');

    $('.message').each(function () {
        var message = $(this);

        message.find('.edit').click(function (event) {
            var body = message.find('.body');
            var uuid = message.attr('data-uuid');

            var show_to_all_element = $(this).parent().find('.show_to_all');

            Swal({
                title: 'Edit comment',
                input: 'textarea',
                inputValue: body.text(),
                showCancelButton: true,
                confirmButtonText: 'Save',
                showLoaderOnConfirm: true,
                html: '<input id="show_to_all_' + uuid + '" name="show_to_all" type="checkbox" value="1" /> ' +
                    '<label for="show_to_all_' + uuid + '">Show to all members</label>',
                onOpen: function () {
                    $('#show_to_all_' + uuid).prop('checked', message.hasClass('show-to-all'));
                },
                preConfirm: function(value) {
                    if (!value) {
                        Swal.showValidationError('Should not be empty!');
                        return false;
                    }

                    return new Promise(function(resolve, reject) {
                        var show_to_all = $('#show_to_all_' + uuid).prop('checked');

                        $.post("/circles/" + circle_uuid + '/messages/' + uuid + '/update', 
                        {
                            'body': value,
                            'show_to_all': show_to_all ? "1" : "0",
                            '_method': 'PUT'
                        }, function(data) {
                            if (data.status == 'success') {

                                body.text(value);
                                
                                if (show_to_all) {
                                    message.addClass('show-to-all');
                                } else {
                                    message.removeClass('show-to-all');
                                }

                                resolve();
                            } else {
                                reject();
                            }
                        }).fail(function () {
                            reject();
                        });
                    }).catch(function (error) {
                        Swal.showValidationError('Can not save the comment.');
                        return false;
                    });
                },
            });

            return false;
        });
    });
}

$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var mouseDown = false;

    $(document).on('mousedown mouseup', function(event) {
        if(event.type == 'mousedown') {
            mouseDown = true;
        } else {
            mouseDown = false;
        }
    });
    
    $('td.check').on('click', function(event) {
        $(this).find('input').prop('checked', !$(this).find('input').prop('checked')).change();
    }).find('input[type="checkbox"]').on('change', function() {
        var parent = $(this).parent('td');
        var value = $(this).prop('checked');
    
        if(value) {
            parent.addClass('checked');
        } else {
            parent.removeClass('checked');
        }
    });

    setup_confirm_dialog();

    set_time_zone();

    setup_edit_dialog();
});
