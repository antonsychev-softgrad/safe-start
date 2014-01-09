Ext.define('SafeStartExt.view.panel.Alerts', {
    extend: 'SafeStartExt.view.abstract.Alerts',
    xtype: 'SafeStartExtPanelAlerts',

    createVehicleStore: function () {
        return SafeStartExt.store.Alerts.create({companyId: this.companyId});
    },

    filterAlerts: function () {
        var searchValue = this.down('textfield[name=search]').getValue();
        var store = this.down('dataview[name=alerts]').getStore();

        store.clearFilter();
        store.filter([{
            property: 'alertDescription',
            value: searchValue
        }]);
    },

    getTBarItems: function () {
        return [{
            xtype: 'textfield',
            name: 'search',
            listeners: {
                change: function (dataview, value) {
                    this.filterAlerts();
                },
                scope: this
            }
        }, {
            xtype: 'button',
            text: 'Refresh',
            handler: function () {
                var store = this.down('dataview[name=alerts]').getStore();
                store.load();
            },
            scope: this
        }];
    }

});
