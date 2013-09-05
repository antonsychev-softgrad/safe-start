Ext.define('SafeStartApp.model.ChecklistAlert', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'images', type: 'auto', defaultValue: []},
            {name: 'status', type: 'string', defaultValue: 'new'}
        ]
    }
});
