Ext.define('SafeStartApp.store.MenuVehicles', {
    extend: 'SafeStartApp.store.AbstractTreeStore',

    requires: [
        'SafeStartApp.model.MenuVehicle'
    ],

    config: {
        defaultRootId: 0,
        model: 'SafeStartApp.model.MenuVehicle',
        proxy: {
            type: "ajax",
            url: 'api/company/getvehicles',
            reader: {
                type: "json",
                rootProperty: "data"
            }
        },

        sorters: 'title',

        grouper: {
            groupFn: function (record) {
                return record.get('title')[0];
            }
        }
    }
});
