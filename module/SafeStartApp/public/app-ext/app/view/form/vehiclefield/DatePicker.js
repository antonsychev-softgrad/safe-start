Ext.define('SafeStartExt.view.form.vehiclefield.DatePicker', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormVehiclefieldDatePicker',
    requires: ['Ext.form.field.ComboBox'],

    name: 'datePicker',

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
                fieldLabel: 'Type',
                name: 'type',
                queryMode: 'local',
                displayField: 'key',
                valueField: 'value',
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
//                xtype: 'datefield',
//                format: SafeStartExt.dateFormat,
//                name: 'defaultValue',
//                fieldLabel: 'Default Value',
//                cls: 'sfa-datepicker',
//                getValue: function () {
//                    var dt = Ext.Date.parse(this.getRawValue(), this.format);
//                    if (dt === undefined) {
//                        return '';
//                    }
//                    if (typeof dt === 'object') {
//                        return parseInt(dt.getTime()/1000, 10);
//                    }
//                    return;
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

        var date = parseFloat(record.get('defaultValue'));
        if (date) {
            this.down('datefield[name=defaultValue]').setValue(new Date(date * 1000));
            this.down('datefield[name=defaultValue]').originalValue = this.down('datefield[name=defaultValue]').getValue();
        }
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