
Ext.define('SafeStartApp.store.Companies', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.Company'
    ],

    config: {
        model: 'SafeStartApp.model.Company',

        proxy: {
            type: "ajax",
            url : "api/admin/getcompanies",
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
