const button = document.querySelector('button.btn');
const form = document.querySelector('form')
form.addEventListener('submit', function (event){
    button.disabled = true
})