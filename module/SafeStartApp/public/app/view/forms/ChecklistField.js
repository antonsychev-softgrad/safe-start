Ext.define('SafeStartApp.view.forms.ChecklistField', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartChecklistFieldForm',
    config: {
        minHeight: 400,
        hidden: true,
        items: [
            {
                xtype: 'hiddenfield',
                name: 'id'
            },
            {
                xtype: 'hiddenfield',
                name: 'parentId'
            },
            {
                xtype: 'hiddenfield',
                name: 'vehicleId'
            },
            {
                xtype: 'textfield',
                label: 'Question',
                required: true,
                name: 'title'
            },
            {
                xtype: 'textfield',
                label: 'Title',
                required: true,
                name: 'description'
            },
            {
                xtype: 'selectfield',
                name: 'type',
                label: 'Type',
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
                        { rank: 'group', title: 'Questions Group'},
                        { rank: 'radio', title: 'Radio Buttons Yes|No|N\A'},
                        { rank: 'checkbox', title: 'Checkbox Yes|No'},
                        { rank: 'text', title: 'Text'},
                        { rank: 'datePicker', title: 'Date Picker'},
                        { rank: 'photo', title: 'Photo'}
                    ]
                },
                listeners: {
                    change: function (field, value) {
                        this.up('SafeStartChecklistFieldForm').changeFieldType(value);
                    }
                }
            },
            {
                xtype: 'togglefield',
                name: 'additional',
                label: 'Additional',
                listeners: {
                    change: function (field, slider, thumb, newValue, oldValue) {

                    }
                }
            },
            {
                xtype: 'selectfield',
                name: 'trigger_value',
                label: 'Trigger filed value',
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
                        { rank: '', title: ''},
                        { rank: 'yes', title: 'Yes'},
                        { rank: 'no', title: 'No'}
                    ]
                }
            },
            {
                xtype: 'textfield',
                label: 'Alert message',
                required: false,
                name: 'alert_title'
            },
            {
                xtype: 'checkboxfield',
                label: 'Alert critical?',
                required: false,
                name: 'alert_critical'
            },
            {
                xtype: 'textfield',
                label: 'Alert description',
                required: false,
                name: 'alert_description'
            },
            {
                xtype: 'spinnerfield',
                maxValue: 1000,
                minValue: 0,
                stepValue: 1,
                name: 'sort_order',
                required: true,
                label: 'Position'
            },
            {
                xtype: 'togglefield',
                name: 'enabled',
                label: 'Enabled'
            },
            {
                xtype: 'toolbar',
                docked: 'bottom',
                items: [
                    {
                        xtype: 'button',
                        name: 'delete-data',
                        text: 'Delete',
                        ui: 'decline',
                        iconCls: 'delete',
                        handler: function () {
                            this.up('SafeStartChecklistFieldForm').fireEvent('delete-data', this.up('SafeStartChecklistFieldForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function () {
                            this.up('SafeStartChecklistFieldForm').fireEvent('save-data', this.up('SafeStartChecklistFieldForm'));
                        }
                    }
                ]
            }
        ]

    },

    setRecord: function (record) {
        if (! record) {
            this.reset();
            return;
        }
        var fields = this.getFields();
        if (record.get('type') == 'root') {
            fields['additional'].show();
            fields['type'].hide();
        } else {
            fields['additional'].hide();
            fields['type'].show();
            fields['additional'].hide();
        }
        this.show();

        this.changeFieldType(record.get('type'));
        this.callParent([record]);
    },

    changeFieldType: function (type) {
        var fields = this.getFields();
        switch (type) {
            case 'group':
                fields['alert_title'].hide();
                fields['alert_critical'].hide();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
            break;
            case 'radio':
            case 'checkbox':
                fields['alert_title'].show();
                fields['alert_critical'].show();
                fields['alert_description'].show();
                fields['trigger_value'].show();
                break;
            default:
                fields['alert_title'].hide();
                fields['alert_critical'].hide();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
                break;
        }
    },

    resetRecord: function () {
        this.reset();
        this.hide();
    }

});
