const searchForm = document.querySelector('#search-form')
const resultBlock = document.querySelector('.wrapper-search-result')
const search_btn = document.querySelector('#search-button')
const closeBtn = document.querySelector('#close-btn')


if (window.innerWidth <= 767) {
    if (searchForm != null) {
        searchForm.classList.add('hidden')
    }
}
if (closeBtn != null) {
    closeBtn.addEventListener('click', function () {
        document.body.classList.remove('overflow-hidden')
        resultBlock.classList.add('hidden');
    })
    searchForm.addEventListener('submit', function (event) {
        event.preventDefault();

        let query = document.getElementById('search-input').value
        resultBlock.classList.remove('hidden');
        document.body.classList.add('overflow-hidden')
        fetch('/search?s=' + encodeURIComponent(query)).then(response => response.text())
            .then(html => document.getElementById('search-result').innerHTML = html)
        search_btn.disabled = false
    })
}