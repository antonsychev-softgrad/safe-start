
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

        sorters: 'firstName',

        grouper: {
            groupFn: function(record) {
                return record.get('firstName')[0];
            }
        }
    }
});
