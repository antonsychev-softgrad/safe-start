Ext.define('SafeStartExt.model.User', {
    extend: "Ext.data.Model",
    fields: [
        {name: 'id', type: 'int', defaultValue: 0},
        {name: 'enabled', type: 'auto', defaultValue: 1},
        {name: 'companyId', type: 'int'},
        {name: 'username', type: 'string'},
        {name: 'role', type: 'string', defaultValue: 'companyUser'},
        {name: 'firstName', type: 'string'},
        {name: 'lastName', type: 'string'},
        {name: 'email', type: 'email'},
        {name: 'position', type: 'string'},
        {name: 'department', type: 'string'}
    ],

    proxy: {
        type: 'memory'
    },

    getFullName: function () {
        return this.get('firstName') + ' ' + this.get('lastName');
    }
});
