Ext.define('SafeStartExt.view.component.Company', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.VehicleList',
        'SafeStartExt.view.panel.VehicleTabs'
    ],
    xtype: 'SafeStartExtComponentCompany',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'Company'
            }, {
                xtype: 'container',
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                flex: 1,
                items: [{
                    xtype: 'SafeStartExtPanelVehicleList',
                    flex: 1,
                    maxWidth: 250
                }, {
                    xtype: 'SafeStartExtPanelVehicleTabs',
                    flex: 2
                }]
            }]
        });
        this.callParent();
    }
});
