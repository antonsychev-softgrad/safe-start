Ext.define('SafeStartApp.view.pages.toolbar.Companies', {

    extend: 'SafeStartApp.view.pages.toolbar.Main',

    xtype: 'SafeStartCompaniesToolbar',

    getToolbarItems: function() {
        return this.toolbarButtons = [{
            iconCls: 'add',
            ui: 'action',
            text: 'Add Company',
            action: 'add-company'
        }, {
            iconCls: 'more',
            ui: 'action',
            isPressed: true,
            handler: function (button) {
                this.config.isPressed = ! this.config.isPressed;
                if (this.config.isPressed) {
                    this.up('SafeStartCompaniesPage').down('list{config.cls == \'sfa-left-container\'}').show();
                    this.addCls(this.getPressedCls());
                } else {
                    this.up('SafeStartCompaniesPage').down('list{config.cls == \'sfa-left-container\'}').hide();
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
        }]
    }
});