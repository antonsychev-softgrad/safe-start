Ext.define('SafeStartApp.view.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'SafeStartMainView',
    requires: [
        'SafeStartApp.model.User',
        'SafeStartApp.view.pages.Auth',
        'SafeStartApp.view.pages.Contact',
        'SafeStartApp.view.pages.Companies',
        'SafeStartApp.view.pages.Company',
        'SafeStartApp.view.pages.SystemSettings',
        'SafeStartApp.view.pages.Vehicles',
        'SafeStartApp.view.pages.Users',
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
