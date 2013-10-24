Ext.define('SafeStartExt.store.MenuVehicles', {
    extend: 'Ext.data.Store',

    requires: [
        'SafeStartExt.model.MenuVehicle'
    ],

    defaultRootId: 0,
    model: 'SafeStartExt.model.MenuVehicle'
});
