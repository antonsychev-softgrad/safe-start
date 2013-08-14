Ext.define('SafeStartApp.model.UserAuth', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'identity',     type: 'string'},
            {name: 'password', type: 'password'}
        ],
        validations: [
            {type: 'presence', name: 'identity', message:"Enter your identity"},
            {type: 'presence', name: 'password', message : "Enter your db password"}
        ]
    }
});
