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

//open form
open_btn.addEventListener('click', function () {
    toggle_search_form()
})
//close form
close_btn.addEventListener('click', function () {
    setTimeout(() => {
        search_group.classList.toggle('fade')
    }, 300)
    toggle_search_form()
    if (header != null){
        header.classList.remove('justify-center')
        header.classList.add('justify-between')
    }

})

search_btn.addEventListener('click', function (event) {
    event.preventDefault()
    search()
});
function search() {
    let query = document.getElementById('search-input').value;
    let resultBlock = document.getElementById('search-wrap');
    let searchResult = document.getElementById('search-result');

    if (!query.trim()) return; // Запобігаємо запитам із пустим рядком

    resultBlock.classList.remove('hidden');
    // document.body.classList.add('overflow-hidden');

    fetch('/search?s=' + encodeURIComponent(query)) // Використовуємо відносний шлях
        .then(response => response.text())
        .then(html => {
            searchResult.innerHTML = html;
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