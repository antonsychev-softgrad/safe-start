Ext.define('SafeStartExt.model.Vehicle', {
    extend: "Ext.data.Model",
    requires: [
    ],
    fields: [
        {name: 'id', type: 'string', defaultValue: 0},
        {name: 'title', type: 'string'},
        {name: 'text', type: 'string'},
        {name: 'type', type: 'string', defaultValue: ''},
        {name: 'registration', type: 'string', defaultValue: ''},
        {name: 'plantId', type: 'string', defaultValue: ''},
        {name: 'projectName', type: 'string', defaultValue: ''},
        {name: 'projectNumber', type: 'string', defaultValue: ''},
        {name: 'currentOdometerKms', defaultValue: 'unknown'},
        {name: 'serviceDueKm', type: 'int', defaultValue: 1000},
        {name: 'serviceThresholdKm', type: 'int', defaultValue: 0},
        {name: 'currentOdometerHours', defaultValue: 'unknown'},
        {name: 'nextServiceDay', defaultValue: 'unknown'},
        {name: 'serviceDueHours', type: 'int', defaultValue: 24},
        {name: 'serviceThresholdHours', type: 'int', defaultValue: 0},
        {name: 'enabled', defaultValue: 1},
        {name: 'warrantyStartDate', type: 'int', defaultValue: new Date()},
        {name: 'expiryDate', type: 'int'},
        {name: 'inspectionDueHours', type: 'int', defaultValue: 24},
        {name: 'inspectionDueKms', type: 'int', defaultValue: 500},
        {name: 'lastInspectionDay', type: 'int', defaultValue: null},
        {name: 'customFields' , type:'auto'},
        {name: 'useKms', type: 'boolean', defaultValue: true},
        {name: 'useHours', type: 'boolean', defaultValue: true},
        {name: 'automaticSending', type: 'boolean', defaultValue: false}
    ],

    proxy: {
        type: 'ajax',
        url: '/api/company/getvehicles',
        reader: {
            type: "json",
            root: "data"
        }
    }
});
