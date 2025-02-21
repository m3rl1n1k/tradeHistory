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

open_btn.addEventListener('click', function () {
    //show input
    toggle_search_form()
    //change type to submit
    //show block with results
    //close block
})
close_btn.addEventListener('click', function () {
    setTimeout(() => {
        search_group.classList.toggle('fade')
    }, 300)
    toggle_search_form()
})

search_btn.addEventListener('submit', function () {
    search()
});


function resolution_condition() {
    if (window.innerWidth <= 767) {
        title.classList.toggle('hidden')
        if (exit != null) {
            exit.classList.toggle('hidden')
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


function search() {
    let query = document.getElementById('search-input').value
    resultBlock.classList.toggle('hidden');
    document.body.classList.add('overflow-hidden')
    fetch('/search?s=' + encodeURIComponent(query)).then(response => response.text())
        .then(html => document.getElementById('search-result').innerHTML = html)
    search_btn.disabled = false
}