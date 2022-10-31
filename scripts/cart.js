$(document).ready(() => {
    // CART
    var productname = Array.from(document.querySelectorAll('.cart_item .productname'));
    var quantity = Array.from(document.querySelectorAll('.cart_item .quantity'));
    var sum = Array.from(document.querySelectorAll('.cart_item .sumProducts'));
    var remove_btn = Array.from(document.querySelectorAll('.remove-cartItem'));
    var total_order_price = document.querySelector('.total-order-price');

    var increment_btn = document.querySelectorAll('.increment-btn');
    var decrement_btn = document.querySelectorAll('.decrement-btn');

    increment_btn.forEach((btn, i) => {
        $(btn).click((e) => {

            var qty = e.target.parentElement.children[1];
            var inputVal = qty.value;
            var newVal = parseInt(inputVal) + 1;
            qty.value = newVal;

            $.ajax({
                type: 'POST',
                url: '../validation/cart-process.php',
                dataType: 'json',
                encode: true,
                data: {
                    product_name: productname[i].innerHTML,
                    quantities: qty.value
                },error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.log(msg);
                }
            }).done((data) => {
                total_order_price.innerHTML = data.ordertotal;
                sum[i].innerHTML = data.subtotal;
            });
        });
    });

    decrement_btn.forEach((btn, i) => {
        $(btn).click((e) => {

            var qty = e.target.parentElement.children[1];

            var inputVal = qty.value;

            var newVal = parseInt(inputVal) - 1;

            qty.value = newVal;

            $.ajax({
                type: 'POST',
                url: '../validation/cart-process.php',
                dataType: 'json',
                encode: true,
                data: {
                    product_name: productname[i].innerHTML,
                    quantities: qty.value
                }
            }).done((data) => {
                total_order_price.innerHTML = data.ordertotal;
                sum[i].innerHTML = data.subtotal;
            });
        });
    });

    quantity.forEach((item, i) => {
        $(item).change(() => {
            $.ajax({
                type: 'POST',
                url: '../validation/cart-process.php',
                dataType: 'json',
                encode: true,
                data: {
                    product_name: productname[i].innerHTML,
                    quantities: item.value
                }
            }).done((data) => {
                total_order_price.innerHTML = data.ordertotal;
                sum[i].innerHTML = data.subtotal;
            })            
        });
    });

    //REMOVE CART ITEM
    remove_btn.forEach((item, i) => {
        $(item).click(() => {
            swal({
                title: "Removing Item: " + productname[i].innerHTML,
                text: "Once deleted, you will not be able to recover.",
                icon: "warning",
                closeOnClickOutside: false,
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: 'POST',
                        url: '../validation/cart-process.php',
                        data: {
                            remove_item: productname[i].innerHTML
                        }
                    });
                    swal("Poof! Your Item has been deleted!", {
                        icon: "success",
                        closeOnClickOutside: false
                    });
                    $('.swal-button--confirm').click(() => {
                        document.location.reload();
                    })
                } else {
                    swal("Your Item is safe!");
                }
            });
        });
    });
})
