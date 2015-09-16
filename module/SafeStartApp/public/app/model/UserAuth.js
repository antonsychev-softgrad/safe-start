Ext.define('SafeStartApp.model.UserAuth', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'username', type: 'string'},
            {name: 'password', type: 'password'}
        ],
        validations: [
            {type: 'presence', name: 'username', message: "Enter your identity"},
            {type: 'presence', name: 'password', message: "Enter your db password"}
        ]
    }
});
