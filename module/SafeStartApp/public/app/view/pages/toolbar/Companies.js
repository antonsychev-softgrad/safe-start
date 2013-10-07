Ext.define('SafeStartApp.view.pages.toolbar.Companies', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompaniesToolbar',

    getToolbarItems: function() {
        this.toolbarButtons = [{
            ui: 'action',
            text: 'Companies'
        }, {
            iconCls: 'more',
            ui: 'action',
            action: 'toggle-menu',
            text: 'Collapse',
            isPressed: true,
            handler: function (button) {
                this.config.isPressed = ! this.config.isPressed;
                if (this.config.isPressed) {
                    this.up('SafeStartCompaniesPage').down('list{config.cls == \'sfa-left-container\'}').show();
                    this.setText('Collapse');
                    this.addCls(this.getPressedCls());
                } else {
                    this.up('SafeStartCompaniesPage').down('list{config.cls == \'sfa-left-container\'}').hide();
                    this.setText('Expand');
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