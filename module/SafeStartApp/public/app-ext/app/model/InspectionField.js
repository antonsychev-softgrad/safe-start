Ext.define('SafeStartExt.model.InspectionField', {
    extend: "Ext.data.Model",
    requires: ['SafeStartExt.model.InspectionAlert'],
    fields: [{
        name: 'id', type: 'int'
    }, {
        name: 'alertCritical', type: 'boolean', mapping: 'alert_critical'
    }, {
        name: 'alertDescription', type: 'string', mapping: 'alert_description'
    }, {
        name: 'alertMessage', type: 'string', mapping: 'alert_title'
    }, {
        name: 'defaultValue', type: 'string', mapping: 'default_value'
    }, {
        name: 'isLeaf', type: 'boolena', defaultValue: true
    }, {
        name: 'description', type: 'string'
    }, {
        name: 'fieldName', type: 'string'
    }, {
        name: 'fieldOrder', type: 'int'
    }, {
        name: 'fieldType', type: 'int'
    }, {
        name: 'fieldId', type: 'int', defaultValue: 0
    }, {
        name: 'type', type: 'string'
    }, {
        name: 'title', type: 'string'
    }, {
        name: 'fieldValue', type: 'string'
    }, {
        name: 'triggerValue', type: 'string', mapping: 'trigger_value'
    }, {
        name: 'additional', type: 'boolean'
    }, {
        name: 'groupName', type: 'string'
    }, {
        name: 'groupOrder', type: 'int'
    }, {
        name: 'sortOrder', mapping: 'sort_order', type: 'int' 
    }, {
        name: 'enabled', type: 'boolean', defaultValue: true
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
