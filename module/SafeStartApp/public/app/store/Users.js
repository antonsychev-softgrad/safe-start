
Ext.define('SafeStartApp.store.Users', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.User'
    ],

    config: {
        model: 'SafeStartApp.model.User',

        proxy: {
            type: "ajax",
            url : "api/company/getusers",
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
