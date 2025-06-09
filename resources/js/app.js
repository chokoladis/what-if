import './bootstrap';
import '../../node_modules/jquery/dist/jquery.min.js';
import '../../node_modules/jquery-mask-plugin/src/jquery.mask.js';
 
$('.js-phone-mask').mask('+9 999 9999 999');

async function setThemeMode(){

    try {
        let settings = {
            method: 'POST',
            headers: {
            //     Accept: 'application/json',
            //     'Content-Type': 'application/json',
                // 'X-CRFS-CONTENT' : $('[csrf-token]').attr('content'),
                'X-CSRF-TOKEN': $('[name="csrf-token"]').attr('content'),
                // body: body,
            }
        };

        let query = await fetch('/ajax/setThemeMode', settings);
        // let json = await query.json();
        console.log(query);
    } catch (error) {
        console.error(error);
    }
}

async function sendAjax(route, method, data){

    let settings = {
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('[name="csrf-token"]').attr('content'),
        },
        body: data,
    };

    let query = await fetch(route, settings);

    let result = await query.json();

    return [query.status, result];
}

$(function(){
    $('.js-change-theme').on('click', function(){

        setThemeMode();

    //     todo
    });

    // $('#modal-feedback [type="submit"]').on('click', function(){
    //     let form = $('#modal-feedback form');
    //     let action = form.attr('action');
    //     let method = form.attr('method');
    //     let formData = form.serializeArray();
    //     let sendData = new FormData();

    //     $.each(formData, function (key, input) {
    //         sendData.append(input.name, input.value);
    //     });

    //     sendAjax(action, method, sendData);
    // });

    $('.js-change-lang .dropdown-item').on('click', function(){
        let sendData = new FormData();
        sendData.append('lang', $(this).data('lang'));

        let data = sendAjax('/setting/lang', 'POST',  sendData);

        data.then(function(result) {
            let status = result[0];
            let json = result[1];
            if (status === 200){
                location.reload();
            }
        });
    });
    

});

