//search
const search_group = document.querySelector('#search-group')
const open_btn = document.querySelector('#open-btn')
const close_btn = document.querySelector('#close-btn')
const search_btn = document.querySelector('#search-btn')
const title = document.querySelector('#title')
const bg = document.querySelector('#form-bg')
const exit = document.querySelector('#exit')
const resultBlock = document.querySelector('#search-result')
const btn_menu = document.querySelector('#menu-btn')
const header = document.querySelector('#header')
const main = document.querySelector('#main-block')
const loader = document.querySelector('#loader')

//open form
open_btn.addEventListener('click', function () {
    main.classList.toggle('hidden')
    toggle_search_form()
    resultBlock.classList.toggle('hidden')
})
//close form
close_btn.addEventListener('click', function () {
    setTimeout(() => {
        search_group.classList.toggle('fade')
    }, 300)
    toggle_search_form()
    main.classList.toggle('hidden')
    if (header != null){
        header.classList.remove('justify-center')
        header.classList.add('justify-between')
    }
    resultBlock.classList.toggle('hidden')

})

search_btn.addEventListener('click', function (event) {
    event.preventDefault()
    search()
});
function search() {
    let query = document.getElementById('search-input').value;
    if (!query.trim()) return; // Запобігаємо запитам із пустим рядком

    resultBlock.classList.remove('hidden');
    // document.body.classList.add('overflow-hidden');
    loader.classList.toggle('hidden')
    if (document.documentElement.className.toString() === 'dark'){
        loader.style.borderTopColor = "rgba(209, 213, 219)"
        loader.style.borderRightColor = "rgba(209, 213, 219)"
        loader.style.borderBottomColor = "rgba(209, 213, 219)"
    }
    else{
        loader.style.borderTopColor = "rgba(55, 65, 81)"
        loader.style.borderRightColor = "rgba(55, 65, 81)"
        loader.style.borderBottomColor = "rgba(55, 65, 81)"
    }
    // Show the loader
    fetch('/search?s=' + encodeURIComponent(query)) // Використовуємо відносний шлях
        .then(response => response.text())
        .then(html => {
            resultBlock.innerHTML = html;
            loader.classList.toggle('hidden')
        })
        .catch(error => console.error('Error:', error));
}

function resolution_condition() {
    if (window.innerWidth <= 767) {
        title.classList.toggle('hidden')
        if (exit != null) {
            exit.classList.toggle('hidden')
        }
        if (header != null) {
            header.classList.add('justify-center')
            header.classList.remove('justify-between')
        }
    }

}

function toggle_search_form() {
    document.body.classList.toggle('overflow-hidden')
    search_group.classList.toggle('hidden')
    search_group.classList.toggle('inline-flex')
    open_btn.classList.toggle('hidden')
    btn_menu.classList.toggle('hidden')
    resolution_condition()
}