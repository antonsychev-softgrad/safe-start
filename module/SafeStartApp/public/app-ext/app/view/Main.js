Ext.define('SafeStartExt.view.Main', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.tab.Panel',
        'Ext.layout.container.Fit',
        'SafeStartExt.view.component.Auth',
        'SafeStartExt.view.component.Contact',
        'SafeStartExt.view.component.Vehicles',
    ],
    
    xtype: 'SafeStartExtMain',
    cls: 'sfa-view-main',

    layout: 'card',

    items: [{
        xtype: 'SafeStartExtComponentAuth'
    }, {
        xtype: 'SafeStartExtComponentContact'
    }, {
        xtype: 'SafeStartExtComponentVehicles'
    }]
});
