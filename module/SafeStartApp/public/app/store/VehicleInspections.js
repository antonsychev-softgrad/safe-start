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
            }
        },

        sorters: 'title',
        pageSize: 10,

        grouper: {
            groupFn: function(record) {
                return record.get('title')[0];
            }
        }
    },

    checkForLastPage: function(store, records, isSuccessful) {
        var pageSize = store.getPageSize();
        var pageIndex = store.currentPage - 1;

        if (isSuccessful && records.length < pageSize) {
            var totalRecords = pageIndex * pageSize + records.length;
            store.setTotalCount(totalRecords);
        } else {
            store.setTotalCount(null);
        }
    },

    constructor: function (config) {
        this.callParent([config]);

        this.addBeforeListener('load', this.checkForLastPage, this);
    }
});
