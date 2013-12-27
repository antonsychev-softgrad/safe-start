Ext.define('SafeStartExt.store.DefaultChecklistTree', {
    extend: 'Ext.data.TreeStore',

    requires: [
        'SafeStartExt.model.InspectionField'
    ],

    model: 'SafeStartExt.model.InspectionField',

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
                url : '/api/admin/getdefaultchecklist',
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
