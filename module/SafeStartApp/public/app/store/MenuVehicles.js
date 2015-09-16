Ext.define('SafeStartApp.store.MenuVehicles', {
    extend: 'SafeStartApp.store.AbstractTreeStore',

    requires: [
        'SafeStartApp.model.MenuVehicle'
    ],

    config: {
        defaultRootId: 0,
        model: 'SafeStartApp.model.MenuVehicle',
        defaultRootProperty: 'data',
        proxy: {
            type: "ajax",
            url: 'api/company/getvehicles',
            reader: {
                type: "json",
                root: "data"
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
