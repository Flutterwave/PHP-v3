$('.make-payment').on('click', ()=> {
    $('.request').text('Processing Payment...');
});


$('.confirm-bank-transfer').on('click', (evt) => {
    evt.preventDefault();

    window.location = `${window.location.origin}/examples/endpoint/verify.php?transactionId=${reference}`;

})

$('.confirm-xaf-transfer-momo').on('click', (evt) => {
    evt.preventDefault();

    window.location = `${window.location.origin}/examples/endpoint/verify.php?tx_ref=${reference}`;
})

$('.check-payment-status').on('click', (evt) => {
    evt.preventDefault();
    window.location.reload();
} )