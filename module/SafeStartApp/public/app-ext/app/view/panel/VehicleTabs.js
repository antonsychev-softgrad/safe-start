Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.tab.Panel',
    requires: [
        'SafeStartExt.view.panel.VehicleInfo'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    border: 0,
    cls: 'sfa-vehicles-tabpanel',

    initComponent: function () {
        var tabs = this.getTabs();
        Ext.apply(this, {
            items: tabs
        });

        this.callParent();

        this.setActiveTab(this.items.first());
    },

    getTabs: function () {
        var me = this, 
            tabs = [];

        this.vehicle.pages().each(function (page) {
            switch (page.get('action')) {
                case 'info': 
                    tabs.push({
                        xtype: 'SafeStartExtPanelVehicleInfo', 
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
    },

    initPage: function (alias, config) {
        if (Ext.ClassManager.getNameByAlias('widget.' + alias) !== '') {
            return {xtype: alias, title: title};
        }

        return {
            xtype: 'panel',
            title: config.title,
            listeners: {
                beforeactivate: function () {
                    this.up('SafeStartExtMain').fireEvent('notSupportedAction');
                    return false;
                }
            }
        };
    }

});
