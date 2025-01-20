$(function () {
    const menu = $("#menu").dxMenu({
        items: [
            {
                icon: 'home',
                text: 'dashboard',
                url: 'dashboard',
        }
        ]
    }).dxMenu('instance');
});