Ext.define('SafeStartExt.view.form.inspectionfield.DatePicker', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormInspectionfieldDatePicker',
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
                fieldLabel: 'Question Text',
                required: true,
                name: 'title'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Short Description',
                required: true,
                name: 'description'
            }, {
                xtype: 'combobox',
                fieldLabel: 'Type',
                name: 'type',
                queryMode: 'local',
                displayField: 'key',
                valueField: 'value',
                store: {
                    fields: ['key', 'value'],
                    data: [{
                        key: 'Checklist Titles Group',
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
                }
            }, {
                xtype: 'datefield',
                format: SafeStartExt.dateFormat,
                name: 'defaultValue',
                fieldLabel: 'Default Value'
            }, {
                xtype: 'numberfield',
                fieldLabel: 'Alert Trigger Value',
                name: 'triggerValue'
            }, {
                xtype: 'checkbox',
                name: 'alertCritical',
                fieldLabel: 'Show Alert In PDF?'
            }, {
                xtype: 'textfield',
                name: 'alertDescription',
                fieldLabel: 'Alert Description'
            }, {
                xtype: 'numberfield',
                maxValue: 1000,
                minValue: 0,
                stepValue: 1,
                name: 'sortOrder',
                required: true,
                fieldLabel: 'Position'
            }, {
                xtype: 'checkbox',
                name: 'enabled',
                fieldLabel: 'Enabled'
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        this.callParent(arguments);
        var date = parseFloat(record.get('defaultValue'));
        if (date) {
            this.down('datefield[name=defaultValue]').setValue(new Date(date * 1000));
        }
    }

});