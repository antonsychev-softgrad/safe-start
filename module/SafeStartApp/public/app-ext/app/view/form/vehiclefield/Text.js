Ext.define('SafeStartExt.view.form.vehiclefield.Text', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormVehiclefieldText',
    requires: ['Ext.form.field.ComboBox'],

    name: 'text',

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
            },{
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
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        // if (record.get('id') === 0) {
        //     this.down('button[name=delete-field]').disable();
        // } else {
        //     this.down('button[name=delete-field]').enable();
        // }
        this.down('field[name=type]').suspendEvents();
        this.callParent(arguments);
        this.down('field[name=type]').resumeEvents();
    }, 

    validate: function () {
        if (Ext.each(this.query('field[required]'), function (field) {
            if (Ext.util.Format.trim('' + field.getValue()).length === 0) {
                Ext.Msg.alert({
                    msg: 'Field ' + field.fieldLabel + ' is required',
                    buttons: Ext.Msg.OK
                });
                return false;
            }
        }) !== true) { // compare with return value of Ext.each
            return false;
        }
        return true;
    }

});