Ext.define('SafeStartApp.view.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'mainTabPanel',

    requires: [
        'SafeStartApp.view.pages.Auth',
        'SafeStartApp.view.pages.Contact'
    ],

    config: {
        tabBarPosition: 'bottom',
        menuItems: []
    },

    initConfig: function() {
        this.addAfterListener('show', this.createMenuTabs);
        this.callParent(arguments);
    },

    createMenuTabs: function() {
        var panelToAdd = [];
        Ext.each(this.config.menuItems, function(item) {
            panelToAdd.push(
                Ext.create('SafeStartApp.view.pages.'+item)
            );
        }, this);
        this.add(panelToAdd);
    }

});
