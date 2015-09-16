Ext.define('SafeStartExt.model.InspectionChecklist', {
    extend: "Ext.data.Model",

    requires: [
        'SafeStartExt.model.InspectionField'
    ],
    fields: [{
        name: 'id', type: 'int'
    }, {
        name: 'additional', type: 'boolean'
    }, {
        name: 'groupName', type: 'string'
    }, {
        name: 'groupOrder', type: 'int'
    }],

    hasMany: [{
        model: 'SafeStartExt.model.InspectionField',
        associationKey: 'fields',
        name: 'items'
    }],

    proxy: {
        type: 'memory'
    }
});
