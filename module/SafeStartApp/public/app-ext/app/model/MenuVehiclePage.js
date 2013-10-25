Ext.define('SafeStartExt.model.MenuVehiclePage', {
    extend: "Ext.data.Model",
    fields: [
        {name: 'id', type: 'string', defaultValue: 0},
        {name: 'text', type: 'string'},
        {name: 'action', type: 'string', defaultValue: ''}
    ]
});
