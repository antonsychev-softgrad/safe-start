Ext.define('SafeStartApp.store.ChecklistAlerts', {
    extend: 'SafeStartApp.store.AbstractStore',

    requires: [
        'SafeStartApp.model.ChecklistAlert'
    ],

    config: {
        model: 'SafeStartApp.model.ChecklistAlert',

        proxy: {
            type: "memory"
        }
    }
});
