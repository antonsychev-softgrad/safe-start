Ext.define('SafeStartApp.model.CompanySubscription', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'users', type: 'int', defaultValue: false},
            {name: 'vehicles', type: 'int', defaultValue: false},
            {name: 'expiry_date', type: 'date', defaultValue: false}
        ],
        validations: [
            {type: 'presence', name: 'users', message:"Please set max allowed users"},
            {type: 'presence', name: 'vehicles', message:"Please set max allowed vehicles"},
            {type: 'presence', name: 'expiry_date', message:"Please set expiry date."}

        ]
    }
});
