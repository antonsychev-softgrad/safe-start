Ext.define('SafeStartApp.view.pages.toolbar.SystemSettings', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartSystemSettingsToolbar',

    getToolbarItems: function () {
        return this.toolbarButtons = [
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ]
    }

});