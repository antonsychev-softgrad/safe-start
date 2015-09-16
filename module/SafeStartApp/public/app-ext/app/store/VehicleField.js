Ext.define('SafeStartExt.store.VehicleField', {
    extend: 'Ext.data.TreeStore',

    requires: [
        'SafeStartExt.model.VehicleField'
    ],

    model: 'SafeStartExt.model.VehicleField',

    root: {
        expanded: true
    },

    sorters: [{
        property: 'sortOrder'
    }],

    constructor: function (config) {
        Ext.apply(this, {
            root: 'data',
            proxy: {
                type: 'ajax',
                url : '/api/company/getvehiclefield',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        if (config.vehicleId) {
            this.proxy.extraParams = this.proxy.extraParams || {};
            this.proxy.extraParams.vehicleId = config.vehicleId;
        }
        this.callParent(arguments);
    }
});
