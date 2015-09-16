Ext.define('SafeStartExt.store.InspectionChecklists', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.InspectionChecklist'
    ],

    pageSize: 25,

    model: 'SafeStartExt.model.InspectionChecklist'

    // constructor: function (config) {
    //     this.setVehicleId(config.vehicleId || 0);
    //     this.callParent([config]);
    // },

    // setVehicleId: function (vehicleId) {
        // this.getProxy().url = SafeStartExt.Ajax.baseHref + 'vehicle/' + vehicleId + '/getchecklist';
    // }
});
