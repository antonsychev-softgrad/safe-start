Ext.define('SafeStartApp.model.ChecklistField', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'text', type: 'string'},
            {name: 'type', type: 'string', defaultValue: ''},
            {name: 'sort_order', type: 'int', defaultValue: 1}
        ],
        validations: [
            {type: 'presence', name: 'title', message: "Vehicle title is required"}
        ]
    }
});
