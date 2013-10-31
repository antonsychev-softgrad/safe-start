Ext.define('SafeStartApp.view.pages.panel.LeftContainer', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartLeftContainer',

    config: {
        layout: 'card',
        name: 'left-container',
        maxWidth: 300
        // animation: 'fade'
    },

    _menuShown: true,

    toggleMenu: function() {
        if (this._menuShown) {
            this.hideMenu();
        } else {
            this.showMenu();
        }
    },
    showMenu: function () {
        this._menuShown = true;
        this.setWidth();
        this.setFlex(1);
        this.setActiveItem(0);
    },
    hideMenu: function () {
        this._menuShown = false;
        this.setFlex('');
        this.setWidth(50);
        this.element.setStyle('flex', '');
        this.setActiveItem(1);
    }
});
