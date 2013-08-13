Ext.define('SafeStartApp.view.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'SafeStartMainView',
    requires: [
        'SafeStartApp.model.User',
        'SafeStartApp.view.pages.Auth',
        'SafeStartApp.view.pages.Contact',
        'SafeStartApp.view.pages.Companies'
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
