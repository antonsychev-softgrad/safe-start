Ext.define('SafeStartApp.view.pages.toolbar.Company', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompanyToolbar',

    getToolbarItems: function () {
         if (SafeStartApp.userModel.get('role') == 'companyUser') {
             this.toolbarButtons = [
                 { xtype: 'spacer' },
                 { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
                 { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
             ]
         } else {
             this.toolbarButtons = [
                 { iconCls: 'add', ui: 'action', text: 'Add Vehicle', action: 'add-vehicle' },
                 { xtype: 'spacer' },
                 { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
                 { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
             ]
         }
        return this.toolbarButtons;
    }

});