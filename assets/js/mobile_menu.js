document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('new')
    const dropdown_list = document.getElementById('dropdown_list')

    btn.addEventListener('click', function () {
        console.log('s')
        dropdown_list.classList.toggle('hidden')
    })
})

document.addEventListener('DOMContentLoaded', function () {
        const burger = document.getElementById('burger')
        const menu = document.getElementById('menu');
        if (window.innerWidth <= 767) {
            if (menu != null) {
                menu.classList.add('hidden')
            }
        }
        burger.addEventListener('click', function () {
            menu.classList.toggle('hidden')
        })
    }
)