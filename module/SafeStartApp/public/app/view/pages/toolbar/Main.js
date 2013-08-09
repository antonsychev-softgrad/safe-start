Ext.define('SafeStartApp.view.pages.toolbar.Main', {

    extend: 'Ext.Toolbar',
    xtype : 'SafeStartMainToolbar',
    requires: ['Ext.field.Search'],

    config: {
        scrollable: {
            direction: 'horizontal',
            indicators: false
        }
    },

    initialize: function () {
        this.callParent();
        this.addListener('resize', function(){
            if (SafeStartApp.userInfo) {
                this.add([
                    { xtype: 'spacer' },
                    { iconCls: 'user', ui: 'action', text: SafeStartApp.getUserInfo().firstName +' '+ SafeStartApp.getUserInfo().lastName, action: 'update_profile'},
                    { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
                ]);
            }
        }, this);

    }

});