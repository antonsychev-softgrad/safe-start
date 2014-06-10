Ext.define('SafeStartExt.model.Inspection', {
    extend: "Ext.data.Model",
    fields: [
        {name: 'checkListId', type: 'int', defaultValue: 0},
        {name: 'title', type: 'string'},
        {name: 'creation_date', type: 'int'},
        {name: 'gps', type: 'string'},
        {name: 'hash', type: 'string'},
        {name: 'id', type: 'string'},
        {name: 'warnings'},
        {name: 'odometer_hours', type: 'string'},
        {name: 'odometer_kms', type: 'string'},
        {name: 'update_date', type: 'int'}
    ],

    proxy: {
        type: 'memory'
    }
});
