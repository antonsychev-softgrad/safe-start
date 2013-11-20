Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.container.Container',
    requires: [
        'SafeStartExt.view.form.Vehicle',
        'SafeStartExt.view.panel.VehicleInspection'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    border: 0,
    cls: 'sfa-vehicles-tabpanel sfa-info-container',
    layout: 'fit',

    initComponent: function () {
        var tabs = this.getTabs();
        Ext.apply(this, {
            items: [{
                xtype: 'tabpanel',
                items: tabs
            }]
        });

        this.callParent();

        this.down('tabpanel').setActiveTab(this.items.first());
    },

    getTabs: function () {
        var tabs = [];

        this.vehicle.pages().each(function (page) {
            switch (page.get('action')) {
                case 'info': 
                    tabs.push({
                        xtype: 'SafeStartExtFormVehicle', 
                        title: page.get('text') 
                    });
                    break;
                case 'inspections':
                    tabs.push({
                        xtype: 'SafeStartExtPanelInspections',
                        title: page.get('text'),
                        vehicle: this.vehicle
                    });
                    break;
                case 'fill-checklist':
                   tabs.push({
                       xtype: 'SafeStartExtPanelVehicleInspection',
                       title: page.get('text'),
                       vehicle: this.vehicle
                   });
                   break;
                // case 'fill-checklist':
                case 'alerts':
                case 'users':
                case 'report':
                case 'update-checklist':
                    tabs.push({
                        xtype: 'container',
                        title: page.get('text'),
                        listeners: {
                            beforeactivate: function () {
                                this.up('SafeStartExtMain').fireEvent('notSupportedAction');
                                return false;
                            }
                        }
                    });
                    break;
            }
        }, this);
        return tabs;
    }

});
