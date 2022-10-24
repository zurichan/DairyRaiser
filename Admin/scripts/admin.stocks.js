$(document).ready(() => {
    var update_stock_btn = Array.from(document.querySelectorAll('.update-stock'));
    var disposed_stock_btn = Array.from(document.querySelectorAll('.dispose-stock'));

    // UPDATING A STOCK
    update_stock_btn.forEach((stock, i) => {
        $(stock).click(() => {
            var index = i + 1;
            var product_id = $('#' + index).children('td[data-target=product_id]').text();
            var location_rack = $('#' + index).children('td[data-target=location_rack]').text();
            var finished_goods = $('#' + index).children('td[data-target=finished_goods]').text();

            // modal
            $('#update_product_stock_id').val(product_id);
            $('#update_rack').val(location_rack);
            $('#update_finished_goods').val(finished_goods);

            $('#update-stocks').modal('toggle');
        });
    });

    // DISPOSING A EXPIRED GOODS
    disposed_stock_btn.forEach((stock, i) => {
        $(stock).click(() => {
            
            var index = i + 1;
            var product_id = $('#' + index).children('td[data-target=product_id]').html();
            var expired_goods = $('#' + index).children('td[data-target=expired_goods]').html();

            console.log(product_id);
            if(expired_goods != 0) {
                // modal
                $('#dispose_stock_id').val(product_id);
                $('#total-expired-goods').html(expired_goods);
               
                $('#dispose-stocks').modal('toggle');
            };
        });
    });
});