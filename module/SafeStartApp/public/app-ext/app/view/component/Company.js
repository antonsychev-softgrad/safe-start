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
                name: 'vehicle-container',
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                flex: 1,
                items: [{
                    xtype: 'SafeStartExtPanelVehicleList',
                    flex: 1,
                    maxWidth: 250
                }]
            }]
        });
        this.callParent();
    },

    setVehicle: function (vehicle) {
        this.unsetVehicle();
        this.down('container[name=vehicle-container]').add({
            xtype: 'SafeStartExtPanelVehicleTabs',
            pagesStore: vehicle.pages(),
            flex: 2
        });
    },

    unsetVehicle: function () {
        var panel = this.down('SafeStartExtPanelVehicleTabs');
        if (panel) {
            panel.destroy();
        }
    }
});
