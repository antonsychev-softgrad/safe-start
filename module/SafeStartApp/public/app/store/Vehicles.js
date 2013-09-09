
Ext.define('SafeStartApp.store.Vehicles', {
    extend: 'SafeStartApp.store.AbstractTreeStore',

    requires: [
        'SafeStartApp.model.Vehicle'
    ],

    config: {
        autoLoad: false,
        model: 'SafeStartApp.model.Vehicle',
        proxy: {
            type: "ajax",
            url : 'api/company/getvehicles',
            reader: {
                type: "json",
                rootProperty: "data"
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
