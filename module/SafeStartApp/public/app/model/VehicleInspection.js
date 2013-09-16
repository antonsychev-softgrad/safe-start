Ext.define('SafeStartApp.model.VehicleInspection', {
    extend: "Ext.data.Model",
    config: {
        fields: [{
            name: 'checkListId', type: 'int'
        }, {
            name: 'title', type: 'string'
        }]
    }
});
