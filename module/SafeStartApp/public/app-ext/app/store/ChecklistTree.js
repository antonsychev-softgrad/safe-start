Ext.define('SafeStartExt.store.ChecklistTree', {
    extend: 'Ext.data.TreeStore',

    requires: [
        'SafeStartExt.model.InspectionField'
    ],

    proxy: {
        type: 'ajax',
        url : '/api/company/getvehiclechecklist',
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    model: 'SafeStartExt.model.InspectionField',

    constructor: function (config) {
        Ext.apply(this, {
            
        });

        this.callParent(arguments);
    }
});
