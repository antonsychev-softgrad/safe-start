Ext.define('SafeStartApp.model.Vehicle', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'text', type: 'string'},
            {name: 'projectName', type: 'string', defaultValue: ''},
            {name: 'projectNumber', type: 'string', defaultValue: ''},
            {name: 'serviceDueKm', type: 'string', defaultValue: ''},
            {name: 'serviceDueHours', type: 'string', defaultValue: ''},
            {name: 'action', type: 'string', defaultValue: ''}
        ],
        associations: [
            {type: 'hasMany', model: 'User', name: 'users'},
            {type: 'hasMany', model: 'User', name: 'responsibleUsers'}
        ],
        validations: [
            {type: 'presence', name: 'title', message: "Company title is required"}
        ]
    }
});
