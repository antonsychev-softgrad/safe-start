Ext.define('SafeStartApp.model.Company', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'email', type: 'string'},
            {name: 'firstName', type: 'string'},
            {name: 'description', type: 'string'},
            {name: 'address', type: 'string'},
            {name: 'phone', type: 'string'},
            {name: 'restricted', type: 'boolean', defaultValue: false},
            {name: 'max_users', type: 'int', defaultValue: false},
            {name: 'max_users', type: 'int', defaultValue: false},
            {name: 'expiry_date', type: 'int'}
        ],
        validations: [
            {type: 'presence', name: 'title', message:"Company title is required"},
            {type: 'presence', name: 'firstName', message:"Responsible person name is required"},
            {type: 'format', field: 'email', matcher: /\S+@\S+\.\S+/, message: "Wrong email format"}
        ]
    }
});
