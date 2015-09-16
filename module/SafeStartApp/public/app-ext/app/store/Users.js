Ext.define('SafeStartExt.store.Users', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.User'
    ],

    proxy: {
        type: 'ajax',
        url : "/api/company/getusers",
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    model: 'SafeStartExt.model.User'
});

