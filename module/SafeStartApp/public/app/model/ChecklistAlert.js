Ext.define('SafeStartApp.model.ChecklistAlert', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'fieldId', type: 'int', defaultValue: 0},
            {name: 'alertMessage', type: 'string'},
            {name: 'triggerValue', type: 'string'},
            {name: 'comment', type: 'string', defaultValue: ''},
            {name: 'photos', type: 'auto', defaultValue: []},
            {name: 'active', type: 'boolean', defaultValue: false}
        ]
    }
});
