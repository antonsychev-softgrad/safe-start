Ext.define('SafeStartExt.model.InspectionAlert', {
    extend: "Ext.data.Model",
    // requires: ['SafeStartExt.model.InspectionField'],
    fields: [
        // {name: 'fieldId', type: 'int', defaultValue: 0},
        {name: 'alertMessage', type: 'string'},
        {name: 'alertDescription', type: 'string'},
        {name: 'critical', type: 'boolean', defaultValue: false},
        {name: 'triggerValue', type: 'string'},
        {name: 'comment', type: 'string', defaultValue: ''},
        {name: 'photos', type: 'auto', defaultValue: null},
        {name: 'active', type: 'boolean', defaultValue: false}
    ],

    proxy: {
        type: 'memory'
    },

    belongsTo: [{
        model: 'SafeStartExt.model.InspectionField',
        associationKey: 'fieldId',
        getterName: 'getField',
        setterName: 'setField',
        name: 'fieldId'
    }]
});


