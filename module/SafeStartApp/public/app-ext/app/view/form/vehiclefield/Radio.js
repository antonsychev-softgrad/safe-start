Ext.define('SafeStartExt.view.form.vehiclefield.Radio', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormVehiclefieldRadio',
    requires: ['Ext.form.field.ComboBox'],

    name: 'radio',

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
                        key: 'Field Titles Group',
                        value: 'group' 
                    }, {
                        key: 'Radio Buttons Yes|No|N/A',
                        value: 'radio'
                    }, {
                        key: 'Checkbox Yes|No',
                        value: 'checkbox'
                    }, {
                        key: 'Label',
                        value: 'label'
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

            }, {
                xtype: 'combobox',
                fieldLabel: 'Default Value',
                name: 'defaultValue',
                displayField: 'key',
                valueField: 'value',
                editable: false,
                store: {
                    fields: ['key', 'value'],
                    data: [{
                        key: 'N/A',
                        value: 'n/a'
                    }, {
                        key: 'NO',
                        value: 'no'
                    }, {
                        key: 'YES',
                        value: 'yes'
                    }]
                }
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
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

        if (this.down('field[name=alertCritical]').getValue()
            && Ext.util.Format.trim(this.down('field[name=alertMessage]').getValue()).length === 0
        ) {
            Ext.Msg.alert({
                msg: 'Field Alert Message is required',
                buttons: Ext.Msg.OK
            });
            return false;
        }
        return true;
    }
});