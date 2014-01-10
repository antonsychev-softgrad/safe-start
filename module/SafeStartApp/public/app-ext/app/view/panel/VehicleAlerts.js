Ext.define('SafeStartExt.view.panel.VehicleAlerts', {
    extend: 'SafeStartExt.view.abstract.Alerts',
    xtype: 'SafeStartExtPanelVehicleAlerts',

    createVehicleStore: function () {
        return SafeStartExt.store.Alerts.create({
            vehicleId: this.vehicle.get('id')
        });
    },

    initComponent: function () {
        this.callParent(arguments);
        var store = this.down('dataview[name=alerts]').getStore();
        store.on('load', function () {
            this.fireEvent('updateAlertsCounter');
        }, this);

        this.on('updateAlertsCounter', function () {
            var counter = 0;
            var title = this.pageConfig.get('text');
            store.each(function (record) {
                if (record.get('status') == 'new') {
                    counter++;
                }
            });
            if (counter) {
                title += ' (' + counter + ')';
            }
            this.pageConfig.set('counter', counter);
            this.setTitle(title);
        }, this);
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
