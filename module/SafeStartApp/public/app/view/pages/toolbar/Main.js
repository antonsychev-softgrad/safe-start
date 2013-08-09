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
                this.add(this.getToolbarItems());
        }, this);
    },

    getToolbarItems: function() {
        return [
            { xtype: 'spacer' },
            { iconCls: 'user', ui: 'action', text: SafeStartApp.userModel.get('firstName') +' '+ SafeStartApp.userModel.get('lastName'), action: 'update_profile'},
            { iconCls: 'action', ui: 'action', text: 'Logout', action: 'logout' }
        ]
    }

});