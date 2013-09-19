Ext.define('SafeStartApp.model.VehicleInspection', {
    extend: "Ext.data.Model",
    config: {
        fields: [{
            name: 'checkListId', 
            type: 'int'
        }, {
            name: 'title', 
            type: 'string'
        }, {
            name: 'creationDate', 
            type: 'datetime',
            mapping: 'creation_date'
        }, {
            name: 'data',
            type: 'auto'
        }, {
            name: 'gps',
            type: 'string'
        }, {
            name: 'id',
            type: 'number'
        }, {
            name: 'hash',
            type: 'string'
        }, {
            name: 'odometerHours',
            type: 'integer',
            mapping: 'odometer_hours'
        }, {
            name: 'odometerKms',
            type: 'integer',
            mapping: 'odometer_kms'
        }]
    }
});
