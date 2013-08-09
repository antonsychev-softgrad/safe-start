Ext.define('SafeStartApp.model.User', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'integer'},
            {name: 'companyId', type: 'integer'},
            {name: 'username', type: 'string'},
            {name: 'role', type: 'string'},
            {name: 'firstName', type: 'string'},
            {name: 'lastName', type: 'string'},
            {name: 'email', type: 'email'}
        ],
        validations: [
            {type: 'presence', name: 'email', message:"Email is required"},
            {type: 'presence', name: 'firstName', message:"Name is required"}
        ]
    }
});
