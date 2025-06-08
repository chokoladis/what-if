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

    // try {
        let settings = {
            method: method,
            headers: {
                'X-CSRF-TOKEN': $('[name="csrf-token"]').attr('content'),
            },
            body: data,
        };

        let query = await fetch(route, settings);

        console.log(query.status);
        
        if (query.status == 201){
            let json = await query.json();

            console.log(json);
        }
        

    // } catch (error) {
        // console.log(query);
        // console.log(error);
        // console.error(error);
    // }
}

$(function(){
    $('.js-change-theme').on('click', function(){

        setThemeMode();
        

        // $(this).
        // $('html').attr('')
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

        let res = sendAjax('/setting/lang', 'POST',  sendData);
        console.log(res);
    //     check result and return errors or upd page
    });
    

});

