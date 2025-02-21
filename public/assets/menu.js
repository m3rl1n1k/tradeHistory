const btn = document.querySelector('#menu-btn')
const open_ico = document.querySelector('#open-menu')
const close_ico = document.querySelector('#close-menu')
const menu = document.querySelector('#menu')


btn.addEventListener('click', function () {
    menu.classList.toggle('hidden')
    open_ico.classList.toggle('hidden')
    close_ico.classList.toggle('hidden')
    menu.classList.toggle('mobile-version')
})