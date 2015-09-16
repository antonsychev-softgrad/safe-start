Ext.define('SafeStartApp.model.Comment', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'content', type: 'string'},
            {name: 'update_date'}
        ],
        associations: [
            {type: 'hasOne', model: 'SafeStartApp.model.User', name: 'user'}
        ]
    }
});
