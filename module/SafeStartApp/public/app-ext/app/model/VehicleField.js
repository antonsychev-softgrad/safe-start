Ext.define('SafeStartExt.model.VehicleField', {
    extend: "Ext.data.Model",

    groupTypes: ['root'],

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
        name: 'isLeaf', type: 'boolean', defaultValue: true
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
        name: 'type', type: 'string', defaultValue: 'text'
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
    }, {
        name: 'vehicleId', type: 'int'
    }],

    hasMany: [{
        model: 'SafeStartExt.model.VehicleField',
        associationKey: 'items',
        name: 'items'
    }],

    proxy: {
        type: 'memory'
    },

    constructor: function (root, id, raw) {
        if (typeof raw == 'object') {
            if (! Ext.Array.contains(this.groupTypes, raw.type)) {
                raw.iconCls = 'sfa-icon-leaf';
            }
        }
        this.callParent(arguments);
    },

    getWriteData: function () {
        var data = this.getData(),
            writeData = {};

        Ext.Object.each(this.fields.map, function (key, value) {
            if (value.mapping) {
                writeData[value.mapping] = data[key];
            } else {
                writeData[key] = data[key];
            }
        });

        return writeData;
    }
});
