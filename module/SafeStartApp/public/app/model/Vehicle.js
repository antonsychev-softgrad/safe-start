Ext.define('SafeStartApp.model.Vehicle', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'text', type: 'string'},
            {name: 'type', type: 'string', defaultValue: ''},
            {name: 'registration', type: 'string', defaultValue: ''},
            {name: 'plantId', type: 'string', defaultValue: ''},
            {name: 'projectName', type: 'string', defaultValue: ''},
            {name: 'projectNumber', type: 'string', defaultValue: ''},
            {name: 'serviceDueKm', type: 'int', defaultValue: 1000},
            {name: 'serviceDueHours', type: 'int', defaultValue: 24},
            {name: 'action', type: 'string', defaultValue: ''},
            {name: 'checkListId', type: 'int', defaultValue: 0},
            {name: 'warrantyStartDate', type: 'int', defaultValue: new Date()}
        ],
        associations: [
            {type: 'hasMany', model: 'SafeStartApp.model.User', name: 'users'},
            {type: 'hasMany', model: 'SafeStartApp.model.User', name: 'responsibleUsers'}
        ],
        validations: [
            {type: 'presence', name: 'title', message: "Vehicle title is required"},
            {type: 'presence', name: 'plantId', message: "Vehicle plantId is required"},
            {type: 'presence', name: 'registration', message: "Vehicle registration number is required"}
        ]
    }
});
