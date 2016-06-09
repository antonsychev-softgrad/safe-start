Ext.define('SafeStartExt.model.Alert', {
    extend: "Ext.data.Model",

    requires: [
        'SafeStartExt.model.User',
        'SafeStartExt.model.Vehicle'
    ],

    fields: [
        {name: 'id', type: 'int'},
        {name: 'alertMessage', type: 'string'},
        {name: 'alertDescription', type: 'string', mapping: 'alert_description'},
        {name: 'creationDate', type: 'string', mapping: 'creation_date'},
        {name: 'dueDate', type: 'string', mapping: 'due_date'},
        {name: 'faultReport', type: 'auto', mapping: 'fault_report'},
        {name: 'status', type: 'string'},
        {name: 'monitor', type: 'boolean'},
        {name: 'images', type: 'auto'},
        {name: 'history', type: 'auto'},
        {name: 'comments', type: 'auto'},
        {name: 'thumbnail', type: 'string', defaultValue: ''}
    ],

    proxy: {
        type: 'memory'
    },

    associations: [{
        type: 'hasOne',
        model: 'SafeStartExt.model.User',
        getterName: 'getUser',
        setterName: 'setUser',
        associationKey: 'user'
    }, {
        type: 'hasOne',
        model: 'SafeStartExt.model.Vehicle',
        getterName: 'getVehicle',
        setterName: 'setVehicle',
        associationKey: 'vehicle'
    }, {
        type: 'hasOne',
        model: 'SafeStartExt.model.InspectionField',
        getterName: 'getField',
        setterName: 'setField',
        associationKey: 'field'
    }]
});


