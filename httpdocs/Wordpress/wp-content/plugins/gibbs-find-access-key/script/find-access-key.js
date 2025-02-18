function enableSubmitButton()
{
    var reservationIdEdit, submitButton;

    reservationIdEdit = document.getElementById('reservationIdEdit');
    submitButton = document.getElementById('submitButton');
    if (reservationIdEdit !== null && submitButton !== null)
    {
        submitButton.disabled = reservationIdEdit.value === '';
    }
}
