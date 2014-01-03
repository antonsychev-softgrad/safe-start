Ext.define('SafeStartExt.view.panel.Alerts', {
    extend: 'SafeStartExt.view.abstract.Alerts',
    xtype: 'SafeStartExtPanelAlerts',

    createVehicleStore: function () {
        return SafeStartExt.store.Alerts.create({companyId: this.companyId});
    }

});
