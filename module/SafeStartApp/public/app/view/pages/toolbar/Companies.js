Ext.define('SafeStartApp.view.pages.toolbar.Companies', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompaniesToolbar',

    getToolbarItems: function () {
        return this.toolbarButtons = [
            { iconCls: 'add', ui: 'action', text: 'Add Company', action: 'add-company' },
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ]
    }

});