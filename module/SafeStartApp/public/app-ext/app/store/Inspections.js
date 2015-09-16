Ext.define('SafeStartExt.store.Inspections', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.Inspection'
    ],

    proxy: {
        type: 'ajax',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    pageSize: 25,

    model: 'SafeStartExt.model.Inspection',

    constructor: function (config) {
        this.setVehicleId(config.vehicleId || 0);
        this.callParent([config]);
    },

    setVehicleId: function (vehicleId) {
        this.getProxy().url = SafeStartExt.Ajax.baseHref + 'vehicle/' + vehicleId + '/getinspections';
    }
});
