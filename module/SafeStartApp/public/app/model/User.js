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
            {type: 'format', field: 'email', matcher: /\S+@\S+\.\S+/, message: "Wrong email format"},
            {type: 'presence', name: 'firstName', message: "Name is required"}
        ]
    }
});
