import './bootstrap';
import $ from 'jquery';
window.jQuery = window.$ = $;

import 'jquery-mask-plugin';

const globalAlert = $('#js-system-alert');

const STATUS_OK= 200;
const STATUS_NO_CONTENT = 204;
const STATUS_BAD_REQUEST = 400;
const STATUS_MANY_REQUESTS = 429;

$('.js-phone-mask').mask('+9 999 9999 999');

async function sendAjax(route, method, data = null) {

    let settings = {
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('[name="csrf-token"]').attr('content'),
        },
    };
    if (data)
        settings.body = data;

    const response = await fetch(route, settings)
    if (response.status === STATUS_MANY_REQUESTS){
        globalAlert.removeClass('d-none')
        globalAlert.find('b').text('Вы отправили слишком много запросов за последнее время, попробуйте позже')
    }

    return response;
}

$(function () {

    $('#modal-feedback [type="submit"]').on('click', function(){

        const modal = $(this).parents('.modal');
        const form = modal.find('form');

        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        let sendData = new FormData();

        $.each(form.serializeArray(), function (key, input) {
            sendData.append(input.name, input.value);
        });

        const invalid = form.find('.is-invalid')
        if (invalid.length){
            for(let i in invalid) {
                if (invalid[i] && invalid[i].classList)
                    invalid[i].classList.remove('is-invalid')
            }
        }

        const pErrors = form.find('p.error')
        if (pErrors.length){
            for(let i in pErrors) {
                if (typeof pErrors[i] != "object")
                    break;

                pErrors[i].remove()
            }
        }

        sendAjax(form.attr('action'), form.attr('method'), sendData)
            .then(function (response) {

                response.json().then((json) => {

                    if (response.status >= STATUS_BAD_REQUEST) {
                        for(let field in json.errors)
                        {
                            let input = form.find(`[name="${field}"]`);
                            input.addClass('is-invalid')
                            json.errors[field].forEach((message) => {
                                input.after(`<p class="error">${message}</p>`)
                            })
                        }
                    } else {
                        modal.find('.js-btn-close').click()

                        globalAlert.removeClass('d-none')
                        globalAlert.find('b').text(json.result)
                    }
                })
            }).catch(function (error) {
                console.log(error)
            })
    });


    $('.js-change-lang .dropdown-item').on('click', function () {

        const sendData = new FormData();
        sendData.append("lang", $(this).data('lang'));

        let data = sendAjax('/setting/lang', 'POST', sendData);

        data.then(function (response) {
            console.log(response)
            if (response.status === STATUS_NO_CONTENT) {
                location.reload();
            }
        });
    });

    $('.js-change-theme').on('click', function () {

        let data = sendAjax('/setting/theme', 'POST');

        data.then(function (response) {

            if (response.status === STATUS_OK) {
                response.json().then((json) => {
                    $('html').attr('data-bs-theme', json.result)
                })
            }
        });
    });

    $('.items-type-output input').on('change', function () {

        const sendData = new FormData();
        sendData.append("type", this.id);

        let data = sendAjax('/setting/type-output', 'POST', sendData);

        // todo for errors
        data.then(function (response) {
            if (response.status === STATUS_OK) {
                location.reload()
            }
        });
    })

    $('.header-search [name="sort"]').on('change', function (){

        const url = new URL(location.href)
        url.searchParams.set('sort', $(this).val())
        location.href = url.href

    })
});