Ext.define('SafeStartExt.store.Inspections', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.Inspection'
    ],

    proxy: {
        type: 'ajax',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    model: 'SafeStartExt.model.Inspection'
});
