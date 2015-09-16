
Ext.define('SafeStartApp.store.AllAlerts', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.Alert'
    ],

    config: {
        model: 'SafeStartApp.model.Alert',

        proxy: {
            type: "ajax",
            url : "api/company/getalerts",
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
