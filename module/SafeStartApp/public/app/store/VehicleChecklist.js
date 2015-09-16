
Ext.define('SafeStartApp.store.VehicleChecklist', {
    extend: 'SafeStartApp.store.AbstractTreeStore',

    requires: [
        'SafeStartApp.model.ChecklistField'
    ],

    config: {
        autoLoad: false,
        model: 'SafeStartApp.model.ChecklistField',
        proxy: {
            type: "ajax",
            url : 'api/company/getvehiclechecklist',
            reader: {
                type: "json",
                rootProperty: "data"
            }
        },

        sorters: 'sort_order',

        grouper: {
            groupFn: function(record) {
                return record.get('sort_order');
            }
        }
    }
});
