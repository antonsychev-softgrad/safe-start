Ext.define('SafeStartApp.view.pages.toolbar.Main', {

    extend: 'Ext.Toolbar',
    xtype: 'SafeStartMainToolbar',
    requires: [
        'SafeStartApp.view.forms.UserProfile'
    ],

    config: {
        scrollable: {
            direction: 'horizontal',
            indicators: false
        }
    },

    initialize: function () {
        this.callParent();
        this.addListener('resize', function () {
            if (!this.toolbarButtons)  this.add(this.getToolbarItems());
        }, this);
    },

    getToolbarItems: function () {
        return this.toolbarButtons = [
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') + ' ' + SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ]
    }

});