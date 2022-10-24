$(document).ready(()=> {
    $('#modal').click((e)=> {
        e.preventDefault();
        $('.modal-container').html('');
        $.ajax({
            type: 'POST',
            url: './validation/guest.php',
            data: {guestauth: true}
        }).done((data)=> {
            $('.modal-container').html(data);
        })
    })
})