$(document).ready(() => {
    console.log('asd');

    $('#current_pass-icon-click').click((e) => {
        e.preventDefault();
        $('#current_pass-eye-icon').toggleClass('bi-eye-slash-fill');

        if($('#current_password').attr('type') === 'password') {
            $('#current_password').attr('type', 'text');
        } else {
            $('#current_password').attr('type', 'password');
        }
    });

    $('#new_pass-icon-click').click((e) => {
        e.preventDefault();
        $('#new_pass-eye-icon').toggleClass('bi-eye-slash-fill');

        if($('#new_password').attr('type') === 'password') {
            $('#new_password').attr('type', 'text');
        } else {
            $('#new_password').attr('type', 'password');
        } 
    });

    $('#rnew_pass-icon-click').click((e) => {
        e.preventDefault();
        $('#rnew_pass-eye-icon').toggleClass('bi-eye-slash-fill');

        if($('#rnew_password').attr('type') === 'password') {
            $('#rnew_password').attr('type', 'text');
        } else {
            $('#rnew_password').attr('type', 'password');
        }
    });
});