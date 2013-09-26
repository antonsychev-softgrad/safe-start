Ext.define('SafeStartApp.model.Contact', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'name', type: 'string'},
            {name: 'email', type: 'string'},
            {name: 'message', type: 'string'}
        ],
        validations: [
            {type: 'presence', name: 'name', message: "Enter your Name"},
            {type: 'presence', name: 'email', message: "Enter your Email"},
            {type: 'presence', name: 'message', message: "Enter your Message"},
            {type: 'format', field: 'email', matcher: /\S+@\S+\.\S+/, message: "Wrong email format"}
        ]
    }
});
