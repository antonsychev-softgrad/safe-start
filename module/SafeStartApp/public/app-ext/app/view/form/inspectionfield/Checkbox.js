Ext.define('SafeStartExt.view.form.inspectionfield.Checkbox', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormInspectionfieldCheckbox',
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
                queryMode: 'local',
                displayField: 'key',
                valueField: 'value',
                fieldLabel: 'Type',
                name: 'type',
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
                },
                listeners: {
                    change: function (combo) {
                        this.fireEvent('onChangeType', this);
                    },
                    scope: this
                }
            }, {
                xtype: 'combobox',
                queryMode: 'local',
                fieldLabel: 'Default Value',
                name: 'defaultValue',
                displayField: 'key',
                valueField: 'value',
                store: {
                    fields: ['key', 'value'],
                    data: [{
                        key: 'NO',
                        value: 'no'
                    }, {
                        key: 'YES',
                        value: 'yes'
                    }]
                }
            }, {
                xtype: 'combobox',
                queryMode: 'local',
                fieldLabel: 'Alert Trigger Value',
                name: 'triggerValue',
                displayField: 'key',
                valueField: 'value',
                store: {
                    fields: ['key', 'value'],
                    data: [{
                        key: 'No Alert Required',
                        value: ''
                    }, {
                        key: 'NO',
                        value: 'no'
                    }, {
                        key: 'YES',
                        value: 'yes'
                    }]
                }
            }, {
                xtype: 'textfield',
                fieldLabel: 'Alert Message',
                name: 'alertMessage'
            }, {
                xtype: 'checkbox',
                name: 'alertCritical',
                fieldLabel: 'Alert Critical?'
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
        this.down('field[name=type]').suspendEvents();
        this.callParent(arguments);
        this.down('field[name=type]').resumeEvents();
    }
});