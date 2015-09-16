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
        this.setCompanyId(config.companyId || 0);
        this.callParent([config]);
    },

    setVehicleId: function (vehicleId) {
        if (vehicleId) {
            this.getProxy().url = SafeStartExt.Ajax.baseHref + 'company/getvehiclealerts';
            this.getProxy().extraParams = {
                vehicleId: vehicleId
            };
        }
    },

    setCompanyId: function (companyId) {
        if (companyId) {
            this.getProxy().url = SafeStartExt.Ajax.baseHref + 'company/getvehiclealerts';
            this.getProxy().extraParams = {
                companyId: companyId 
            };
        }
    }
});
