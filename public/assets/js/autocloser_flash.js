function fadeOutAndRemove(element) {
    element.classList.add("fade-out");
    setTimeout(() => {
        element.remove();
    }, 2000); // Чекаємо 3 секунди перед видаленням
}

document.addEventListener('DOMContentLoaded', function (){
    let flash = document.querySelector('[data-autoclose]')

    if (flash !== null){
        fadeOutAndRemove(flash)
    }
})