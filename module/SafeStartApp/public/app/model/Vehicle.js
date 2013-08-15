Ext.define('SafeStartApp.model.Vehicle', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'}
        ],
        validations: [
            {type: 'presence', name: 'title', message:"Company title is required"}
        ]
    }
});
