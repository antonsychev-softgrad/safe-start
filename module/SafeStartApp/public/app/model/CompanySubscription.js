Ext.define('SafeStartApp.model.CompanySubscription', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'max_users', type: 'int', defaultValue: 0},
            {name: 'max_vehicles', type: 'int', defaultValue: 0},
            {name: 'expiry_date', type: 'date', defaultValue: new Date()}
        ],
        validations: [
            {type: 'presence', name: 'max_users', message:"Please set max allowed users"},
            {type: 'presence', name: 'max_vehicles', message:"Please set max allowed vehicles"}
        ]
    }
});
