Ext.define('SafeStartApp.store.Alerts', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.Alert'
    ],

    config: {
        model: 'SafeStartApp.model.Alert',

        proxy: {
            type: "ajax",
            url : "api/company/getvehiclealerts",
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
