Ext.define('SafeStartApp.model.ChecklistField', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'parentId', type: 'int', defaultValue: 0},
            {name: 'vehicleId', type: 'int', defaultValue: 0},
            {name: 'enabled', type: 'int', defaultValue: 1},
            {name: 'title', type: 'string'},
            {name: 'text', type: 'string'},
            {name: 'type', type: 'string', defaultValue: ''},
            {name: 'sort_order', type: 'int', defaultValue: 1},
            {name: 'trigger_value', type: 'string', defaultValue: 1},
            {name: 'alert_title', type: 'string', defaultValue: ''}
        ],
        validations: [
            {type: 'presence', name: 'title', message: "Field title is required"}
        ]
    }
});
