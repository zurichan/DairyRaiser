$(document).ready(() => {

    // VARIABLES DECLARATIONS
    var remove_product = Array.from(document.querySelectorAll('.remove-product'));
    var update_product = Array.from(document.querySelectorAll('.update-product'));
    var productcode = Array.from(document.querySelectorAll('.productcode'));
    var productname = Array.from(document.querySelectorAll('.productname'));


    // REMOVING A PRODUCT
    remove_product.forEach((product, i) => {
        $(product).click(() => {
            swal({
                title: "Removing Product: " + productname[i].innerHTML,
                text: "Once deleted, you will not be able to recover.",
                icon: "warning",
                closeOnClickOutside: false,
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var remove_product_pass = 'pass';
                    $.ajax({
                        type: 'POST',
                        url: '../process/process-product.php',
                        data: {
                            remove_product: remove_product_pass,
                            product_code: productcode[i].innerHTML
                        },
                        success: ((data) => {
                        })
                    })
                    swal("Poof! Your Product has been deleted!", {
                        icon: "success",
                        closeOnClickOutside: false
                    });
                    $('.swal-button--confirm').click(() => {
                        document.location.reload();
                    })
                } else {
                    swal("Your Product is safe!");
                }
            });
        });
    });

    // UPDATING A PRODUCT
    update_product.forEach((product, i) => {
        $(product).click(() => {
            var index = i + 1;
            var product_name = $('#' + index).children('td[data-target=productname]').text();
            var productcode = $('#' + index).children('td[data-target=productcode]').text();
            var productflavor = $('#' + index).children('td[data-target=flavorname]').text();
            var description = $('#' + index).children('td[data-target=description]').text();
            var orig_price = $('#' + index).children('td[data-target=price]').text();
           
            var price_2 = orig_price.replace('.00', '');
            var price = price_2.replace('₱', '');

            // modal
            $('#productname_update').val(product_name);
            $('#product_id_update').val(productcode);
            $('.prev-flavor').html('previous flavor: ' + productflavor);
            $('#description_update').val(description);
            $('#productprice_update').val(price);

            $('#update-product').modal('toggle');
        });
    })
})
