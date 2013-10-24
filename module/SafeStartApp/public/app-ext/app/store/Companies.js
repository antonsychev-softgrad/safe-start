Ext.define('SafeStartExt.store.Companies', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.Company'
    ],

    proxy: {
        type: 'ajax',
        url : '/api/admin/getcompanies',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    model: 'SafeStartExt.model.Company'
});
