Ext.define('SafeStartExt.store.Alerts', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.Alert'
    ],

    proxy: {
        type: 'ajax',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    pageSize: 25,

    model: 'SafeStartExt.model.Alert',

    constructor: function (config) {
        this.setVehicleId(config.vehicleId || 0);
        this.callParent([config]);
    },

    setVehicleId: function (vehicleId) {
        this.getProxy().url = SafeStartExt.Ajax.baseHref + 'company/getvehiclealerts';
        this.getProxy().extraParams = {
            vehicleId: vehicleId
        };
    }
});