let activeMenu = null;
let hideTimeout = null;

function showMenu(menuId) {
    if (activeMenu) hideMenu(activeMenu);
    const menu = document.getElementById(menuId);
    menu.classList.add('show');
    activeMenu = menuId;
}

function hideMenu(menuId) {
    const menu = document.getElementById(menuId);

    setTimeout(() => {
        menu.classList.remove('show');
        if (activeMenu === menuId) activeMenu = null;
    }, 2000);
}

