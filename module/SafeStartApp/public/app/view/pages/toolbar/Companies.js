Ext.define('SafeStartApp.view.pages.toolbar.Companies', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompaniesToolbar',

    getToolbarItems: function() {
        this.toolbarButtons = [{
            ui: 'action',
            text: 'Companies'
        }, {
            xtype: 'spacer'
        }, {
            iconCls: 'user',
            ui: 'action',
            text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'),
            action: 'update_profile'
        }, {
            iconCls: 'action',
            ui: 'action',
            text: 'Logout',
            action: 'logout'
        }];
        return this.toolbarButtons;
    }
});