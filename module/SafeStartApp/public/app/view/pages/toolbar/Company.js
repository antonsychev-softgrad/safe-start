Ext.define('SafeStartApp.view.pages.toolbar.Company', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompanyToolbar',

    getToolbarItems: function () {
        this.toolbarButtons = [
            {
                iconCls: 'more',
                ui: 'action',
                action: 'toggle-menu',
                isPressed: true,
                handler: function (button) {
                    this.config.isPressed = !this.config.isPressed;
                    if (this.config.isPressed) {
                        this.up('SafeStartCompanyPage').down('nestedlist{config.cls == \'sfa-left-container\'}').show();
                        this.addCls(this.getPressedCls());
                    } else {
                        this.up('SafeStartCompanyPage').down('nestedlist{config.cls == \'sfa-left-container\'}').hide();
                        this.removeCls(this.getPressedCls());
                    }
                }
            },
            {
                xtype: 'spacer'
            },
            {
                iconCls: 'user',
                ui: 'action',
                text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'),
                action: 'update_profile'
            },
            {
                iconCls: 'action',
                ui: 'action',
                text: 'Logout',
                action: 'logout'
            }
        ];
        return this.toolbarButtons;
    }

});