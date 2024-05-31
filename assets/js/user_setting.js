let form = document.getElementsByName('setting_user')
const switchBothCatWithOutColor = document.getElementById('setting_user_categoriesWithoutColor');
const switchCatWithColor = document.getElementById('setting_user_coloredCategories')
const switchParCatWithColor = document.getElementById('setting_user_coloredParentCategories')

let status = [
    switchCatWithColor,
    switchParCatWithColor
]
const toggleColorMode = e => {
    console.log(status)
    if (switchBothCatWithOutColor.checked) {
        switchCatWithColor.checked = false
        switchParCatWithColor.checked = false
    }
    console.log(status)
    if (!switchBothCatWithOutColor.checked) {
        switchCatWithColor.checked = status[0].checked.valueOf()
        switchParCatWithColor.checked = status[1].checked.valueOf()
    }
    console.log(status)


}

switchBothCatWithOutColor.addEventListener('change', toggleColorMode)
