Ext.define('SafeStartExt.model.InspectionField', {
    extend: "Ext.data.Model",
    requires: ['SafeStartExt.model.InspectionAlert'],
    fields: [{
        name: 'id', type: 'int'
    }, {
        name: 'alertCritical', type: 'boolean'
    }, {
        name: 'alertDescription', type: 'string'
    }, {
        name: 'alertMessage', type: 'string'
    }, {
        name: 'defaultValue', type: 'string'
    }, {
        name: 'fieldDescription', type: 'string'
    }, {
        name: 'fieldName', type: 'string'
    }, {
        name: 'fieldOrder', type: 'int'
    }, {
        name: 'fieldType', type: 'int'
    }, {
        name: 'type', type: 'string'
    }, {
        name: 'fieldValue', type: 'string'
    }, {
        name: 'triggerValue', type: 'string'
    }, {
        name: 'additional', type: 'boolean'
    }, {
        name: 'groupName', type: 'string'
    }, {
        name: 'groupOrder', type: 'int'
    }],

    hasMany: [{
        model: 'SafeStartExt.model.InspectionField',
        associationKey: 'items',
        name: 'items'
    }, {
        model: 'SafeStartExt.model.InspectionAlert',
        associationKey: 'alerts',
        name: 'alerts'
    }],

    proxy: {
        type: 'memory'
    }
});