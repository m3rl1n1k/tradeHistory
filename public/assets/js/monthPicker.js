let month_picker = document.getElementById('budget_month')

document.addEventListener('DOMContentLoaded', function () {
    month_picker.addEventListener('focus', function () {
        if (month_picker !== null) {
            month_picker.type = 'month'
        }
    })
})