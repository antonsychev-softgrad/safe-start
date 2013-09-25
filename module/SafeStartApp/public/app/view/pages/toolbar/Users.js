Ext.define('SafeStartApp.view.pages.toolbar.Users', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartUsersToolbar',

    getToolbarItems: function () {
        return this.toolbarButtons = [{
            iconCls: 'add',
            ui: 'action',
            text: 'Add User',
            action: 'add-user'
        }, {
            iconCls: 'more',
            ui: 'action',
            action: 'toggle-menu',
            isPressed: true,
            handler: function (button) {
                this.config.isPressed = ! this.config.isPressed;
                if (this.config.isPressed) {
                    this.up('SafeStartUsersPage').down('list{config.cls == \'sfa-left-container\'}').show();
                    this.addCls(this.getPressedCls());
                } else {
                    this.up('SafeStartUsersPage').down('list{config.cls == \'sfa-left-container\'}').hide();
                    this.removeCls(this.getPressedCls());
                }
            }
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
    }

});