let form = document.getElementsByName('setting_user')
const switchBothCatWithOutColor = document.getElementById('setting_user_categoriesWithoutColor');
const switchCatWithColor = document.getElementById('setting_user_coloredCategories')
const switchParCatWithColor = document.getElementById('setting_user_coloredParentCategories')

const saveStatus = e => {
    localStorage.setItem('switchCatWithColor', switchCatWithColor.checked)
    localStorage.setItem('switchParCatWithColor', switchParCatWithColor.checked)
}
const toggleColorMode = e => {
    if (switchBothCatWithOutColor.checked) {
        switchCatWithColor.checked = false
        switchCatWithColor.disabled = true
        switchParCatWithColor.checked = false
        switchParCatWithColor.disabled = true
    }
    if (!switchBothCatWithOutColor.checked) {
        switchCatWithColor.checked = JSON.parse(localStorage.getItem("switchCatWithColor"))
        switchCatWithColor.disabled = false
        switchParCatWithColor.checked = JSON.parse(localStorage.getItem("switchParCatWithColor"))
        switchParCatWithColor.disabled = false
    }


}

saveStatus()
switchBothCatWithOutColor.addEventListener('change', toggleColorMode)
