$(document).ready(function () {
    if($('.flash').on(':visible')) {
        setTimeout(() => {
            $('.flash').remove();
        }, 5000)
    }

    $('.remove-button').on('click', function (e) {
        if(window.confirm('Naozaj si prajete zmazať túto položku? ')) {
            return true;
        }
        return false;
    });
});