Ext.define('SafeStartApp.store.VehicleInspections', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.VehicleInspection'
    ],

    config: {
        model: 'SafeStartApp.model.VehicleInspection',

        proxy: {
            type: "ajax",
            reader: {
                type: "json",
                rootProperty: "data"
            },
            extraParams: {
                limit: 10
            }
        },

        sorters: 'title',

        grouper: {
            groupFn: function(record) {
                return record.get('title')[0];
            }
        }
    }
});
