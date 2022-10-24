$(document).ready(() => {
    var update_material_btn = Array.from(document.querySelectorAll('.update-materials'));
    var remove_material_btn = Array.from(document.querySelectorAll('.remove-materials'));

    // UPDATING A PRODUCT
    update_material_btn.forEach((materials, i) => {
        $(materials).click(() => {
            var index = i + 1;
            var material_id = $('#' + index).children('td[data-target=material_id]').text();
            var supplier = $('#' + index).children('td[data-target=supplier]').text();
            var material = $('#' + index).children('td[data-target=raw_materials]').text();
            var price_text = $('#' + index).children('td[data-target=material_price]').text();
            var stocks = $('#' + index).children('td[data-target=material_stocks]').text();

            var price_replace = price_text.replace('.00', '');
            var price = price_replace.replace('₱', '');

            // modal
            $('#update_material_id').val(material_id);
            $('#update_supplier').val(supplier);
            $('#update_material').val(material);
            $('#update_price').val(price);
            $('#update_material_stocks').val(stocks);

            $('#update-materials').modal('toggle');
        });
    });

    //REMOVING MATERIALS
    var raw_materials = Array.from(document.querySelectorAll('.raw_materials'));
    remove_material_btn.forEach((materials, i) => {
        $(materials).click(() => {
            swal({
                title: "Removing Materials: " + raw_materials[i].innerHTML,
                text: "Once deleted, you will not be able to recover.",
                icon: "warning",
                closeOnClickOutside: false,
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: 'POST',
                        url: '../process/process-materials.php',
                        data: {
                            raw_material: raw_materials[i].innerHTML
                        }
                    })
                    swal("Poof! Your Material has been deleted!", {
                        icon: "success",
                        closeOnClickOutside: false
                    });
                    $('.swal-button--confirm').click(() => {
                        document.location.reload();
                    });
                }
            });
        });
    });
});