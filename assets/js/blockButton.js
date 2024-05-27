const button = document.querySelector('button.btn');
let form = document.querySelector('form')
form.addEventListener('submit', function (event) {
    button.disabled = true
})