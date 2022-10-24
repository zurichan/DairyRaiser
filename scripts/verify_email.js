
var verification_token = document.querySelector('#verification_token');

function maxlength(verification_token) {
    if (verification_token.value.length > 6) {
        verification_token.value = verification_token.value.slice(0, 6);
    };
}

function Resend_Token() {

    var email = $('.activating_email').text();

    $.ajax({
        method: 'POST',
        url: '../../validation/resend_token.php',
        data: {
            resend_token: email 
        },
        datatype: 'JSON',
        success: function (data) {

          var message =  $.parseJSON(data); 

           switch(message.message) {

            case 'attempts':
                swal({
                    title: "Too may verification token attempts",
                    text: "",
                    icon: "error",
                    closeOnClickOutside: false
                })
                break;

            case 'limit':
                swal({
                    title: "Verification Token Sent Limit",
                    text: "Too many verification token has been send as of today. Please wait for 24 hours.",
                    icon: "error",
                    closeOnClickOutside: false
                })
                break;

            case 'expired':
                swal({
                    title: "Verification Token is not yet expired",
                    text: "",
                    icon: "error",
                    closeOnClickOutside: false
                })
                break;

            case 'resend_success':
                swal({
                    title: "Resend Verification Token Successfully!",
                    text: "We have send a new verification token to your email address.",
                    icon: "success",
                    closeOnClickOutside: false
                })
                break;
           }
        }
    });
}