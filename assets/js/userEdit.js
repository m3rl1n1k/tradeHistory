const inputFields = document.querySelectorAll('.edit');
const submitButton = document.getElementById('user_edit');

document.addEventListener('DOMContentLoaded', function () {
    submitButton.addEventListener('click', function (event) {

        inputFields.forEach(function (inputField) {
            inputField.removeAttribute('disabled');
        });

        submitButton.addEventListener('mouseout', function (event) {
            submitButton.setAttribute('type', 'submit');
            submitButton.textContent = "Save";
        })
    });
});