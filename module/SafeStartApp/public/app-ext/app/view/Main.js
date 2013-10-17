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

    layout: 'fit',

    items: [{
        xtype: 'tabpanel',
        items: [{
            xtype: 'SafeStartExtComponentAuth',
            title: 'Auth',
            html: 'hello safestart'
        }, {
            xtype: 'SafeStartExtComponentContact',
            title: 'Contact'
        }, {
            xtype: 'SafeStartExtComponentVehicles',
            title: 'Vehicles'
        }]
    }]
});
