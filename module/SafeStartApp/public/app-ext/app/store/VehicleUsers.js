Ext.define('SafeStartExt.store.VehicleUsers', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.User'
    ],

    proxy: {
        type: 'ajax',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    model: 'SafeStartExt.model.User',

    constructor: function (config) {
        this.setVehicleId(config.vehicleId || 0);
        this.callParent([config]);
    },

    setVehicleId: function (vehicleId) {
        this.getProxy().url = SafeStartExt.Ajax.baseHref + 'vehicle/' + vehicleId + '/users';
    }
});

