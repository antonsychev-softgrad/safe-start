Ext.define('SafeStartApp.view.pages.toolbar.Users', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartUsersToolbar',

    getToolbarItems: function () {
        return this.toolbarButtons = [
            { iconCls: 'add', ui: 'action', text: 'Add User', action: 'add-user' },
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ]
    }

});