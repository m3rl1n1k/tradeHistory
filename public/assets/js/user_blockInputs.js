const inputFields = document.querySelectorAll('.edit');
const submitButton = document.getElementById('user_edit');

document.addEventListener('DOMContentLoaded', function () {
    submitButton.addEventListener('click', function () {

        inputFields.forEach(function (inputField) {
            inputField.removeAttribute('disabled');
        });

        submitButton.addEventListener('mouseout', function () {
            submitButton.setAttribute('type', 'submit');
            submitButton.textContent = "Send";
        })
    });

    const form = document.getElementsByName('feedback')
    const inputArea = document.querySelector('#feedback_message')

    inputArea.innerHTML = ''
    form.reset()

});
