Ext.define('SafeStartApp.model.Company', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'integer'},
            {name: 'title', type: 'string'}
        ],
        validations: [
            {type: 'presence', name: 'title', message:"Title is required"}
        ]
    }
});
