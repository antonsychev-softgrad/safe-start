Ext.define('SafeStartExt.view.Main', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.tab.Panel',
        'Ext.layout.container.Fit',
        'SafeStartExt.view.component.Auth',
        'SafeStartExt.view.component.Companies',
        'SafeStartExt.view.component.Company',
        'SafeStartExt.view.component.Users',
        'SafeStartExt.view.component.Contact'
    ],
    
    xtype: 'SafeStartExtMain',
    cls: 'sfa-view-main',

    layout: 'card'
});
