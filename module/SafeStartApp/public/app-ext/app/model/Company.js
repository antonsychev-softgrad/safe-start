Ext.define('SafeStartExt.model.Company', {
    extend: "Ext.data.Model",
    fields: [
        {name: 'id', type: 'int', defaultValue: 0},
        {name: 'title', type: 'string'},
        {name: 'email', type: 'string'},
        {name: 'logo', type: 'string'},
        {name: 'firstName', type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'address', type: 'string'},
        {name: 'phone', type: 'string'},
        {name: 'restricted', type: 'boolean', defaultValue: false},
        {name: 'unlim_expiry_date', type: 'boolean', defaultValue: false},
        {name: 'unlim_users', type: 'boolean', defaultValue: false},
        {name: 'max_users', type: 'int', defaultValue: 0},
        {name: 'max_vehicles', type: 'int', defaultValue: 0},
        {name: 'expiry_date', type: 'int'},
        {name: 'other_users', type: 'array', defaultValue: []}
    ],

    proxy: {
        type: 'memory'
    }

});
