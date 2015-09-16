Ext.define('SafeStartExt.model.MenuVehicle', {
    extend: "SafeStartExt.model.Vehicle",
    requires: [
        'SafeStartExt.model.MenuVehiclePage'
    ],
    hasMany: [{
        model: 'SafeStartExt.model.MenuVehiclePage',
        associationKey: 'data',
        name: 'pages'
    }]
});
