const hide_user_sum = document.querySelectorAll('.replace-block')
const btn_switch = document.getElementById('switch')
const eye_open = document.getElementById('eye-open')
const eye_close = document.getElementById('eye-close')
const starts = document.querySelectorAll('.stars')

if (localStorage.getItem('hidden_sum')) {
    switch_eye()
    hide_sum()
}

function switch_eye() {
    eye_open.classList.toggle('hidden')
    eye_close.classList.toggle('hidden')

}

function hide_sum() {
    hide_user_sum.forEach(function (el) {
        starts.forEach(function (star) {
            el.classList.toggle('hidden')
            star.classList.toggle('hidden')
            return false;
        })
    })
}

if (hide_user_sum != null) {
    btn_switch.addEventListener('click', function () {
        if (localStorage.getItem('hidden_sum')) {
            localStorage.setItem('hidden_sum', 'false')
        } else {
            localStorage.setItem('hidden_sum', 'true')
        }
        switch_eye()
        hide_sum()
    })

}