Ext.define('SafeStartApp.view.pages.toolbar.CompanySettings', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompanySettingsToolbar',

    getToolbarItems: function () {
        this.toolbarButtons = [
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ];
        return this.toolbarButtons;
    }

});