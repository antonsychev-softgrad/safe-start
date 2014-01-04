Ext.define('SafeStartExt.view.panel.VehicleAlerts', {
    extend: 'SafeStartExt.view.abstract.Alerts',
    xtype: 'SafeStartExtPanelVehicleAlerts',

    createVehicleStore: function () {
        return SafeStartExt.store.Alerts.create({vehicleId: this.vehicle.get('id')});
    }
});
