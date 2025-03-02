let month_picker = document.getElementById('budget_month')

document.addEventListener('DOMContentLoaded', function (){
    if (month_picker !== null){
        month_picker.value = ''
        month_picker.type = 'month'
    }
})