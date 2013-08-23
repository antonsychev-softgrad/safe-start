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
            {name: 'serviceDueKm', type: 'int', defaultValue: 1},
            {name: 'serviceDueHours', type: 'int', defaultValue: 1},
            {name: 'action', type: 'string', defaultValue: ''}
        ],
        associations: [
            {type: 'hasMany', model: 'User', name: 'users'},
            {type: 'hasMany', model: 'User', name: 'responsibleUsers'}
        ],
        validations: [
            {type: 'presence', name: 'title', message: "Vehicle title is required"}
        ]
    }
});
