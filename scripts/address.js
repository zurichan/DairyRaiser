$(document).ready(() => {

    var make_default_btn = Array.from(document.querySelectorAll('.make-default-btn'));
    var remove_address_btn = Array.from(document.querySelectorAll('.remove-address-btn'));
    var complete_address = Array.from(document.querySelectorAll('.complete-address'));
    var address_id = Array.from(document.querySelectorAll("[data-target='address_id']"));
    /** MAKE DEFAULT ADDRESS */
    make_default_btn.forEach((default_btn, i) => {
        $(default_btn).click(() => {
            swal({
                title: "Make Default This Address ?",
                text: complete_address[i].innerHTML,
                closeOnClickOutside: false,
                buttons: true,
                dangerMode: true,
            }).then((Default) => {
                if (Default) {
                    $.ajax({
                        type: 'POST',
                        url: '../../../validation/address-process.php',
                        dataType: 'json',
                        encode: true,
                        data: {
                            make_default_address: address_id[i].value
                        },
                        error: function (request, error) {
                            console.log(arguments);
                            console.log(error);                        },
                       success: function(data) {
                        console.log('asd');
                        
                       }
                    }).done((response) => {
                        if (response) {
                            swal({
                                title: response,
                                icon: "error",
                                closeOnClickOutside: false
                            });
                        } else {
                            swal("Default Address has been Updated", {
                                icon: "success",
                                closeOnClickOutside: false
                            });
                            $('.swal-button--confirm').click(() => {
                                document.location.reload();
                            })
                        }
                    });
                }
            });
        })
    });

    /** EDIT ADDRESS */
    // edit_address_btn.forEach((edit_btn, i) => {
    //     $(edit_btn).click(() => {
    //         $.ajax({
    //             type: 'POST',
    //             url: '../../../validation/address-process.php',
    //             dataType: 'json',
    //             data: {type: 'POST',
    //                     url: '../../../validation/address-process.php',
    //                     dataType: 'json',
    //             encode: true,
    //             success: function (data) {
    //                 $.map(data.province_list, (key, value) => {
    //                     $('#update_province').append("<option value=" + value + ">" + value + "</option>");
    //                 });
    //             }
    //         });


    //         $('#update-address').modal('toggle');
    //     })
    // })

    /** REMOVE ADDRESS BUTTON */
    remove_address_btn.forEach((remove_btn, i) => {
        $(remove_btn).click(() => {
            swal({
                title: "Remove this Address",
                text: complete_address[i].innerHTML,
                closeOnClickOutside: false,
                buttons: true,
                dangerMode: true,
            }).then((Default) => {
                if (Default) {
                    $.ajax({
                        type: 'POST',
                        url: '../../../validation/address-process.php',
                        dataType: 'json',
                        encode: true,
                        data: {
                            remove_address: address_id[i].value
                        }
                    }).done((response) => {
                        console.log(response)
                        if (response) {
                            swal({
                                title: response,
                                icon: "error",
                                closeOnClickOutside: false
                            });
                        } else {
                            swal("Address has been Deleted.", {
                                icon: "success",
                                closeOnClickOutside: false
                            });
                            $('.swal-button--confirm').click(() => {
                                document.location.reload();
                            })
                        }
                    });
                }
            });
        })
    });

    // LOAD PROVINCE
    $.ajax({
        type: 'POST',
        url: '../../../validation/address-process.php',
        dataType: 'json',
        data: { select_province: 'true' },
        encode: true,
        success: function (data) {
            $.map(data, (key, value) => {
                $('#province').append("<option value=" + key['province_id'] + ">" + key['province_name'] + "</option>");
            })
        }
    });

    //LOAD MUNICIPALITY
    $('#province').change(() => {
        $.ajax({
            type: 'POST',
            url: '../../../validation/address-process.php',
            dataType: 'json',
            data: { select_municipality: $('#province').val() },
            encode: true,
            success: function (data) {
                $('#municipality').empty();
                $('#municipality').append('<option value="none">Select Municipality</option>');
                $('#barangay').empty();
                $('#barangay').append('<option value="None">Select Barangay</option>');
                $.each(data, (key, value) => {
                    $('#municipality').append("<option value=" + value['municipality_id'] + ">" + value['municipality_name'] + "</option>");
                })
            }
        })
    });

    //LOAD BARANGAY
    $('#municipality').change(() => {
        $.ajax({
            type: 'POST',
            url: '../../../validation/address-process.php',
            dataType: 'json',
            data: { select_barangay: $('#municipality').val() },
            encode: true,
            success: function (data) {
                $('#barangay').empty();
                $('#barangay').append('<option value="None">Select Barangay</option>');
                $.each(data, (key, value) => {
                    console.log(value['barangay_id']);
                    $('#barangay').append("<option value=" + value['barangay_id'] + ">" + value['barangay_name'] + "</option>");
                })
            }
        })
    });
});

var postal_code = document.querySelector('#postal_code');

var invalidChars = [
    "-",
    "+",
    "e",
];

postal_code.addEventListener("keydown", function (e) {
    if (invalidChars.includes(e.key)) {
        e.preventDefault();
    }
});

function maxlength(postal_code) {
    if (postal_code.value.length > 4) {
        postal_code.value = postal_code.value.slice(0, 4);
    };
};