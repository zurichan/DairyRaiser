$(document).ready(function () {

    // first name
    $('#fname').change(function () {
        if ($('#fname').val() != '') {
            $('#fname').removeClass('is-valid');
            $('#fname').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: { fname_id: $('#fname').val() },
                dataType: 'json',
                encode: true,
            }).done(function() {
                console.log(data);
                // $('#fname').toggleClass(data);    
            });

        } else {
            $('#fname').removeClass('is-valid');
            $('#fname').removeClass('is-invalid');
        }
    });

    // last name
    $('#lname').change(function () {
        if ($('#lname').val() != '') {
            $('#lname').removeClass('is-valid');
            $('#lname').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: { lname_id: $('#lname').val() },
                success: function (data) {
                    $('#lname').toggleClass(data);
                }
            });
        } else {
            $('#lname').removeClass('is-valid');
            $('#lname').removeClass('is-invalid');
        }
    });

    // email
    $('#email').change(function () {
        if ($('#email').val() != '') {
            $('#email').removeClass('is-valid');
            $('#email').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: { email_id: $('#email').val() },
                success: function (data) {
                    $('#email').toggleClass(data);
                }
            });
        } else {
            $('#email').removeClass('is-valid');
            $('#email').removeClass('is-invalid');
        }
    });

    //postal
    $('#postal').keypress(function () {
        if (this.value.length === 4) {
            return false;
        }
    });
    $('#postal').change(function () {
        if ($('#postal').val() != '') {
            $('#postal').removeClass('is-valid');
            $('#postal').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: {
                    postal_id: $('#postal').val(),
                    postal_len: $('#postal').val().length
                },
                success: function (data) {
                    $('#postal').toggleClass(data);
                }
            });
        } else {
            $('#postal').removeClass('is-valid');
            $('#postal').removeClass('is-invalid');
        }
    });

    // Street Name, House, Building No.
    $('#locInfo').change(function () {
        if ($('#locInfo').val() != '') {
            $('#locInfo').removeClass('is-valid');
            $('#locInfo').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: { locInfo_id: $('#locInfo').val() },
                success: function (data) {
                    $('#locInfo').toggleClass(data);
                }
            });
        } else {
            $('#locInfo').removeClass('is-valid');
            $('#locInfo').removeClass('is-invalid');
        }
    });

    //phone number
    $('#pNum').keypress(function () {
        if (this.value.length === 11) {
            return false;
        }
    });
    $('#pNum').change(function () {
        if ($('#pNum').val() != '') {
            var pNum_fdigit = String($('#pNum').val())[0];
            var pNum_sdigit = String($('#pNum').val())[1];
            $('#pNum').removeClass('is-valid');
            $('#pNum').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: {
                    pNum_id: $('#pNum').val(),
                    pNum_len: $('#pNum').val().length,
                    pNum_fdigit: pNum_fdigit,
                    pNum_sdigit: pNum_sdigit
                },
                success: function (data) {
                    $('#pNum').toggleClass(data);
                }
            });
        } else {
            $('#pNum').removeClass('is-valid');
            $('#pNum').removeClass('is-invalid');
        }
    });

    // Password
    $('#password').change(function () {
        if ($('#password').val() != '') {
            $('#password').removeClass('is-valid');
            $('#password').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: './configs/process.php',
                data: {
                    password_id: $('#password').val(),
                    password_len: $('#password').val().length
                },
                success: function (data) {
                    $('#password').addClass(data);
                }
            });
        } else {
            $('#password').removeClass('is-valid');
            $('#password').removeClass('is-invalid');
        }
    });
    // observe for re type password
    $('#password').keyup(function () {
        if ($('#password').val() != '' && $('#password').hasClass("is-valid")) {
            $('#rpassword').removeClass('is-valid');
            $('#rpassword').removeClass('is-invalid');

            if ($('#password').val() === $('#rpassword').val()) {
                $('#rpassword').addClass('is-valid');
            } else {
                $('#rpassword').addClass('is-invalid');
            }
        } else {
            $('#rpassword').removeClass('is-valid');
            $('#rpassword').removeClass('is-invalid');
        }
    });
    // retype password
    $('#rpassword').keyup(function () {

        if ($('#password').val() != '' && $('#password').hasClass("is-valid")) {
            $('#rpassword').removeClass('is-valid');
            $('#rpassword').removeClass('is-invalid');

            if ($('#password').val() === $('#rpassword').val()) {
                $('#rpassword').addClass('is-valid');
            } else {
                $('#rpassword').addClass('is-invalid');
            }
        } else {
            $('#rpassword').removeClass('is-valid');
            $('#rpassword').removeClass('is-invalid');
        }
    });

});

//Address

// 1. province

function loadCity(id) {
    $.ajax({
        type: 'POST',
        url: './configs/process.php',
        data: { province_id: id },
        success: function (data) {
            $('#city').html(data);
        }
    });
}

// 2. city

function loadBrgy(id) {
    $.ajax({
        type: 'POST',
        url: './configs/process.php',
        data: { city_id: id },
        success: function (data) {
            $('#barangay').html(data);
        }
    });
}

//              S U B M I T !
/*
1. submit Form
2. check if theres an error
3. display an error via class
4. if theres an error, dont continue to submit
5. if correct proceed

*/













