Ext.define('SafeStartApp.view.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'SafeStartMainView',
    requires: [
        'SafeStartApp.model.User',
        'SafeStartApp.view.abstract.dialog'
    ],
    items: [],
    config: {
        tabBarPosition: 'bottom',
        items: [
            { xclass: 'SafeStartApp.view.pages.Auth'},
            { xclass: 'SafeStartApp.view.pages.Contact'}
        ]
    }
});
