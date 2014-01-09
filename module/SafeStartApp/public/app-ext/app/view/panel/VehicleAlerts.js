Ext.define('SafeStartExt.view.panel.VehicleAlerts', {
    extend: 'SafeStartExt.view.abstract.Alerts',
    xtype: 'SafeStartExtPanelVehicleAlerts',

    createVehicleStore: function () {
        return SafeStartExt.store.Alerts.create({
            vehicleId: this.vehicle.get('id')
        });
    },

    filterAlerts: function () {
        var searchValue = this.down('textfield[name=search]').getValue();
        var statusValue = this.down('combobox[name=status-filter]').getValue();
        var store = this.down('dataview[name=alerts]').getStore();

        store.clearFilter();
        store.filter([{
            property: 'alertDescription',
            value: searchValue
        }, {
            property: 'status',
            value: statusValue
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
            xtype: 'combobox',
            queryMode: 'local',
            name: 'status-filter',
            select: 0,
            value: '',
            editable: false,
            store: {
                proxy: 'memory',
                data: [{
                    title: 'All',
                    value: ''
                }, {
                    title: 'New',
                    value: 'new'
                }, {
                    title: 'Closed',
                    value: 'closed'
                }],
                fields: ['title', 'value']
            },
            displayField: 'title',
            valueField: 'value',
            listeners: {
                change: function (combobox, value) {
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
