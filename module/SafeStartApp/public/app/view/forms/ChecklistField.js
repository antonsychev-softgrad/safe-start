Ext.define('SafeStartApp.view.forms.ChecklistField', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    requires: ['SafeStartApp.model.ChecklistField'],
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
                name: 'is_root'
            },
            {
                xtype: 'hiddenfield',
                name: 'vehicleId'
            },
            {
                xtype: 'textfield',
                label: 'Question Text',
                required: true,
                name: 'title'
            },
            {
                xtype: 'textfield',
                label: 'Short Description',
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
                        { rank: 'group', title: 'Checklist Titles Group'},
                        { rank: 'radio', title: 'Radio Buttons Yes|No|N/A'},
                        { rank: 'checkbox', title: 'Checkbox Yes|No'},
                        { rank: 'label', title: 'Label'},
                        { rank: 'text', title: 'Text'},
                        { rank: 'datePicker', title: 'Date Picker'}
                    ]
                },
                listeners: {
                    change: function (field, value) {
                        this.up('SafeStartChecklistFieldForm').changeFieldType(value);
                    }
                }
            },
            {
                xtype: 'textfield',
                name: 'default_value',
                label: 'Default Value'
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
                label: 'Alert Trigger Value',
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
                        { rank: '', title: 'No Alert Required'},
                        { rank: 'n/a', title: 'N/A'},
                        { rank: 'yes', title: 'Yes'},
                        { rank: 'no', title: 'No'}
                    ]
                }
            },
            {
                xtype: 'textfield',
                label: 'Alert Message',
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
                label: 'Alert Description',
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
                            var form = this.up('SafeStartChecklistFieldForm');
                            var values = form.getValues();
                            if (values.type == 'radio' || values.type == 'checkbox') {
                                if (values.alert_critical && ! values.alert_title) {
                                    Ext.Msg.alert('Alert message is required');
                                    return;
                                }
                            }
                            this.up('SafeStartChecklistFieldForm').fireEvent('save-data', this.up('SafeStartChecklistFieldForm'));
                        }
                    }
                ]
            }
        ]

    },

    initialize: function () {
        this.callParent([]);
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
            fields['type'].show();
            fields['additional'].hide();
        }
        this.show();

        this._record = record;
        this.changeFieldType(record.get('type'));

        this.callParent([record]);

        if (record.get('type') === 'datePicker') {
            var date = new Date(record.get('default_value') * 1000);
            if (date) {
                this.down('field[name=default_value]').setValue(date);
            }
        }
        
        if (record.get('type') == 'root') {
            fields.is_root.setValue(true);
        }
    },

    switchDefaultValueField: function (type) {
        var field = this.down('field[name=default_value]');
        var index = this.items.indexOf(field);
        var value = this._record.get('default_value');
        if (field) this.remove(field);
        switch (type) {
            case 'group': 
            case 'root':
                field = {
                    xtype: 'hiddenfield'
                };
            break;
            case 'radio':
                field = {
                    xtype: 'selectfield',
                    valueField: 'rank',
                    displayField: 'title',
                    value: this._record.get('default_value'),
                    store: {
                        data: [
                            { rank: 'n/a', title: 'N/A'},
                            { rank: 'no', title: 'No'},
                            { rank: 'yes', title: 'Yes'}
                        ]
                    }
                };
                break;
            case 'checkbox':
                field = {
                    xtype: 'selectfield',
                    valueField: 'rank',
                    displayField: 'title',
                    value: this._record.get('default_value'),
                    store: {
                        data: [
                            { rank: 'no', title: 'No'},
                            { rank: 'yes', title: 'Yes'}
                        ]
                    }
                };
                break;
            case 'datePicker':
                field = {
                    xtype: 'datepickerfield',
                    picker: {
                        yearTo: 2024,
                        yearFrom: 2000
                    },
                    dateFormat: SafeStartApp.dateFormat
                };
                if (value) {
                    field.value = new Date(value);
                } 
                break;
            default:
                field = {
                    xtype: 'textfield'
                };
        }
        field.name = 'default_value';
        field.label = 'Default Value';
        this.insert(index, field);
    },

    switchTriggerValueField: function (type) {
        var field = this.down('field[name=trigger_value]');
        var index = this.items.indexOf(field);
        this.remove(field);
        switch (type) {
            case 'datePicker':
                field = {
                    xtype: 'textfield',
                    name: 'trigger_value',
                    label: 'Alert Trigger Value',
                    step: 1,
                    value: this._record.get('trigger_value')
                };
                break;
            default: 
                field = {
                    xtype: 'selectfield',
                    name: 'trigger_value',
                    label: 'Alert Trigger Value',
                    valueField: 'rank',
                    displayField: 'title',
                    value: this._record.get('trigger_value'),
                    store: {
                        data: [
                            { rank: '', title: 'No Alert Required'},
                            { rank: 'yes', title: 'Yes'},
                            { rank: 'no', title: 'No'},
                            { rank: 'n/a', title: 'N/A'}
                        ]
                    }
                };
                break;
        }
        this.insert(index, field);
    },

    switchAlertCriticalMessage: function (type) {
        if (type == 'datePicker') {
            this.down('field[name=alert_critical]').setLabel('Show Alert In PDF');
            this.down('field[name=trigger_value]').setLabel('Remind Days');
        } else {
            this.down('field[name=alert_critical]').setLabel('Alert Critical?');
            this.down('field[name=trigger_value]').setLabel('Alert Trigger Value');
        }
    },

    changeFieldType: function (type) {
        this.switchDefaultValueField(type);
        this.switchAlertCriticalMessage(type);
        this.switchTriggerValueField(type);
        var fields = this.getFields();
        switch (type) {
            case 'group':
                fields['alert_title'].hide();
                fields['alert_critical'].hide().disable();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
                fields['description'].show();
                fields['default_value'].show();
                fields['title'].setLabel('Question Text');
                fields['description'].setLabel('Short Description');
                fields['description'].setRequired(true);
            break;
            case 'radio':
            case 'checkbox':
                fields['alert_title'].show();
                fields['alert_critical'].show().enable();
                fields['alert_description'].show();
                fields['trigger_value'].show();
                fields['description'].show();
                fields['default_value'].show();
                fields['title'].setLabel('Question Text');
                fields['description'].setLabel('Short Description');
                fields['description'].setRequired(true);
                break;
            case 'datePicker':
                fields['alert_critical'].show().enable();
                fields['alert_description'].show();
                fields['trigger_value'].show();
                fields['description'].show();
                fields['default_value'].show();
                fields['title'].setLabel('Question Text');
                fields['description'].setLabel('Short Description');
                fields['description'].setRequired(true);
                break;
            case 'label':
                fields['alert_title'].hide();
                fields['alert_critical'].hide().disable();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
                fields['description'].hide();
                fields['default_value'].hide();
                fields['title'].setLabel('Label');
                fields['description'].setLabel('Short Description');
                fields['description'].setRequired(true);
                break;
            case 'root':
                fields['alert_title'].hide();
                fields['alert_critical'].hide().disable();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
                fields['description'].show();
                fields['default_value'].show();
                fields['title'].setLabel('Checklist Title');
                fields['description'].setLabel('Title To Display In Report');
                fields['description'].setRequired(false);
                break;
            default:
                fields['alert_title'].hide();
                fields['alert_critical'].hide().disable();
                fields['alert_description'].hide();
                fields['trigger_value'].hide();
                fields['description'].show();
                fields['default_value'].show();
                fields['title'].setLabel('Question Text');
                fields['description'].setLabel('Short Description');
                fields['description'].setRequired(true);
                break;
        }
    },

    resetRecord: function () {
        try {
            //this.reset();
        } catch (ignore) {
        }
        this.hide();
    }

});
