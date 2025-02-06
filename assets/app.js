/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
import {startStimulusApp} from '@symfony/stimulus-bridge';
import './styles/app.css';


import './js/bootstrap.bundle.min.js'
import './js/DayNight_mode.js'

const searchForm = document.querySelector('#search-form')
const resultBlock = document.querySelector('.wrapper-search-result')
const closeBtn = document.querySelector('#close-btn')

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

})


const button = document.querySelector('button.btn');
const search_btn = document.querySelector('#search-button')
const form = document.querySelector('form')
if (form != null && form.id === 'search-form') {
    console.log(form.id)
    form.addEventListener('submit', function () {
        search_btn.disabled = false
    })
}
if (form != null) {
    form.addEventListener('submit', function () {
        button.disabled = true
        setTimeout(function () {
            search_btn.disabled = false
        }, 3000)
    })
}

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.([jt])sx?$/
));
