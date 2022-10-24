$(document).ready(() => {
    
    // DECREMENT

    $('.decrement-btn').click((e) => {
        
        e.preventDefault();

        var qty = $('#quantity').val();

        var value = parseInt(qty, 20);

        value = isNaN(value) ? 0 : value;

        if (value > 0) {
            value--;
            $('#quantity').val(value);
        }
    });

    // INCREMENT

    $('.increment-btn').click((e) => {

        e.preventDefault();

        var qty = $('#quantity').val();

        var value = parseInt(qty, 20);

        value = isNaN(value) ? 0 : value;

        if (value < 10) {
            value++;
            $('#quantity').val(value);
        }
    });
});