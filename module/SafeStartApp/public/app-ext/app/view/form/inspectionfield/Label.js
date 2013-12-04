Ext.define('SafeStartExt.view.form.inspectionfield.Label', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormInspectionfieldLabel',
    requires: [],

    name: 'label',

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
                fieldLabel: 'Label',
                required: true,
                name: 'title'
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
