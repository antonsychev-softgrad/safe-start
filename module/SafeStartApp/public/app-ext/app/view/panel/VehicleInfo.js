Ext.define('SafeStartExt.view.panel.VehicleInfo', {
    extend: 'Ext.view.View',
    requires: [
    ],
    xtype: 'SafeStartExtPanelVehicleInfo',
    layout: {
        type: 'vbox'
    },
    itemSelector: 'table tr',

    initComponent: function () {
        Ext.apply(this, {
            tpl: new Ext.XTemplate(
                '<table style="min-width: 400px; font-size: 18px; color: #344; margin: 10px">',
                '<tpl for=".">',
                '<tr">',
                    '<td>{key}</td>',
                    '<td>{value}</td>',
                '</tr>',
                '</tpl>',
                '</table>'
            ),
            store: {
                proxy: {
                    type: 'memory'
                },
                fields: ['key', 'value']
            }
        });
        this.callParent();
    },


    setVehicleInfo: function (vehicle) {
        var serviceDue = vehicle.get('serviceDueKm') + ' kms ' +
            vehicle.get('serviceDueHours') + ' hours';
        var currentOdometer = vehicle.get('currentOdometerKms') + ' kms ' +
            vehicle.get('currentOdometerHours') + ' hours';
        var inspectionDue = vehicle.get('inspectionDueKms') + ' kms ' +
            vehicle.get('inspectionDueHours') + ' hours';

        this.getStore().loadData([{
            key: 'Title:', 
            value: vehicle.get('title')
        }, {
            key: 'Next Service Day:', 
            value: vehicle.get('nextServiceDay')
        }, {
            key: 'Type:', 
            value: vehicle.get('type')
        }, {
            key: 'Plant ID:', 
            value: vehicle.get('plantId')
        }, {
            key: 'Project name:', 
            value: vehicle.get('projectName')
        }, {
            key: 'Project number:', 
            value: vehicle.get('projectNumber')
        }, {
            key: 'Enabled:', 
            value: vehicle.get('enabled') ? 'Yes': 'No'
        }, {
            key: '&nbsp;', value: ''
        }, {
            key: 'Until next serviceDue:', 
            value: serviceDue 
        }, {
            key: 'Current Odometer:',
            value: currentOdometer
        }, {
            key: 'Until next inspection due:',
            value: inspectionDue
        }]);
    }
});
