Ext.define('SafeStartApp.store.ChecklistAlerts', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.ChecklistAlert'
    ],

    config: {
        model: 'SafeStartApp.model.ChecklistAlert',

        proxy: {
            type: "ajax",
            url : "api/company/getvehiclealerts",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        },

     //   sorters: 'title',

        grouper: {
            groupFn: function(record) {
                //return record.get('title')[0];
            }
        }
    }
});
