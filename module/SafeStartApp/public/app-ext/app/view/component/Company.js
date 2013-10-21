Ext.define('SafeStartExt.view.component.Company', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.view.VehicleList'
    ],
    xtype: 'SafeStartExtComponentCompany',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'TODO: Company title'
            }, {
                xtype: 'container',
                layout: 'hbox',
                flex: 1,
                maxWidth: 250,
                items: [{
                    xtype: 'SafeStartExtViewVehicleList'
                }]
            }]
        });
        this.callParent();
    }
});
