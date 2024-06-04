const button = document.querySelector('button.btn');
const form = document.querySelector('form')
if (form != null) {
    form.addEventListener('submit', function (event) {
        button.disabled = true
    })
}
