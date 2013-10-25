Ext.define('SafeStartExt.view.component.Company', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav'
    ],
    xtype: 'SafeStartExtComponentCompany',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',

    items: [{
        xtype: 'SafeStartExtContainerTopNav',
        width: '100%'
    }]
});
