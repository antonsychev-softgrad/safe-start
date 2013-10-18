Ext.define('SafeStartExt.view.component.Company', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav'
    ],
    xtype: 'SafeStartExtComponentCompany',

    items: [{
        xtype: 'SafeStartExtContainerTopNav'
    }]
});
