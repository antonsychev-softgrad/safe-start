Ext.define('SafeStartApp.view.pages.toolbar.Users', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartUsersToolbar',

    getToolbarItems: function () {
        this.toolbarButtons = [{
            iconCls: 'more',
            ui: 'action',
            action: 'toggle-menu',
            text: 'Collapse Menu',
            isPressed: true,
            handler: function (button) {
                this.config.isPressed = ! this.config.isPressed;
                if (this.config.isPressed) {
                    this.up('SafeStartUsersPage').down('list{config.cls == \'sfa-left-container\'}').show();
                    this.setText('Collapse Menu');
                    this.addCls(this.getPressedCls());
                } else {
                    this.up('SafeStartUsersPage').down('list{config.cls == \'sfa-left-container\'}').hide();
                    this.setText('Expand Menu');
                    this.removeCls(this.getPressedCls());
                }
            },
            listeners: {
                initialize: function () {
                    this.addCls(this.getPressedCls()); 
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
        return this.toolbarButtons;
    }

});