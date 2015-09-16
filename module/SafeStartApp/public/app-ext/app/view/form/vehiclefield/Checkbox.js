Ext.define('SafeStartExt.view.form.vehiclefield.Checkbox', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormVehiclefieldCheckbox',
    requires: ['Ext.form.field.ComboBox'],

    name: 'checkbox',

    initComponent: function() {
        Ext.apply(this, {
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            }, {
                xtype: 'hiddenfield',
                name: 'parentId'
            }, {
                xtype: 'hiddenfield',
                name: 'vehicleId'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Field Text',
                required: true,
                name: 'title'
            }, {
                xtype: 'combobox',
                queryMode: 'local',
                displayField: 'key',
                valueField: 'value',
                fieldLabel: 'Type',
                name: 'type',
                editable: false,
                store: {
                    fields: ['key', 'value'],
                    data: [{
                        key: 'Checkbox Yes|No',
                        value: 'checkbox'
                    }, {
                        key: 'Text',
                        value: 'text'
                    }, {
                        key: 'Date Picker',
                        value: 'datePicker'
                    }]
                },
                listeners: {
                    change: function (combo) {
                        this.fireEvent('onChangeType', this);
                    },
                    scope: this
                }
            }
//                {
//                xtype: 'combobox',
//                queryMode: 'local',
//                fieldLabel: 'Default Value',
//                name: 'defaultValue',
//                displayField: 'key',
//                valueField: 'value',
//                editable: false,
//                store: {
//                    fields: ['key', 'value'],
//                    data: [{
//                        key: 'NO',
//                        value: 'no'
//                    }, {
//                        key: 'YES',
//                        value: 'yes'
//                    }]
//                }
//            }
            ]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        this.down('field[name=type]').suspendEvents();
        this.callParent(arguments);
        this.down('field[name=type]').resumeEvents();
    }


});