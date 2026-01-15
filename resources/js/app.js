import './bootstrap';
import '../../node_modules/jquery/dist/jquery.min.js';
import '../../node_modules/jquery-mask-plugin/src/jquery.mask.js';

$('.js-phone-mask').mask('+9 999 9999 999');

async function sendAjax(route, method, data = null) {

    let settings = {
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('[name="csrf-token"]').attr('content'),
        },
    };
    if (data) {
        settings.body = data;
    }

    let query = await fetch(route, settings);

    let result = await query.json();

    return [query.status, result];
}

$(function () {

    $('#modal-feedback [type="submit"]').on('click', function(){
        let form = $('#modal-feedback form');
        let action = form.attr('action');
        let method = form.attr('method');

        let formData = form.serializeArray();
        let sendData = new FormData();

        $.each(formData, function (key, input) {
            sendData.append(input.name, input.value);
        });

        const dataPromise = sendAjax(action, method, sendData);

        dataPromise.then(function (result) {
            let status = result[0];
            let json = result[1];

            // todo
            if (status === 422) {

            } else {
                    // json.result
            }
        }
        ).catch(function (error) {
            console.log(error)
        })
    });


    $('.js-change-lang .dropdown-item').on('click', function () {

        $.ajax({
            url: '/setting/lang',
            type: 'POST',
            data: {
                "_token": $('[name="csrf-token"]').attr('content'),
                lang: $(this).data('lang')
            },
            success: function (json) {
                // console.log(data);

                if (json.success) {
                    location.reload();
                } else {
                    // $('')
                }
            }
        });
    });

    $('.js-change-theme').on('click', function () {

        let data = sendAjax('/setting/theme', 'POST');

        data.then(function (result) {
            let status = result[0];
            let json = result[1];
            if (status === 200 && json.result) {
                $('html').attr('data-bs-theme', json.result)
            }
        });
    });

    $('.items-type-output input').on('change', function () {

        const sendData = new FormData();
        sendData.append("type", this.id);

        let data = sendAjax('/setting/type-output', 'POST', sendData);

        data.then(function (result) {
            let status = result[0];
            let json = result[1];
            if (status === 200 && json.success) {
                location.reload()
            }
        });
    })
});