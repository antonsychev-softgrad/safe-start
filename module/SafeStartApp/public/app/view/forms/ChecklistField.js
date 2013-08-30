Ext.define('SafeStartApp.view.forms.ChecklistField', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartChecklistFieldForm',
    config: {
        minHeight: 400,
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
                label: 'Title',
                required: true,
                name: 'title'
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
                    change: function (field, slider, thumb, newValue, oldValue) {
                        this.up('SafeStartChecklistFieldForm').showCreateFieldCategory();
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
        ],

        listeners: {
            change: function (form, record, xz, eOpts) {
                if (!record || !record.get('parentId') || record.get('type') == 'root') {
                    form.showCreateRootCategory();
                } else {
                    form.showCreateFieldCategory();
                }
            }
        }
    },

    showCreateRootCategory: function () {
        this.getFields()['additional'].show();
        this.getFields()['type'].setValue('root');
        this.getFields()['type'].hide();
        this.getFields()['alert_title'].hide();
        this.getFields()['trigger_value'].hide();
        this.getFields()['trigger_value'].setValue('yes');
    },

    showCreateFieldCategory: function () {
        this.getFields()['type'].show();
        this.getFields()['additional'].hide();
        switch (this.getFields()['type'].getValue()) {
            case 'group':
                this.getFields()['alert_title'].hide();
                this.getFields()['trigger_value'].hide();
            break;
            case 'radio':
            case 'checkbox':
                this.getFields()['alert_title'].show();
                this.getFields()['trigger_value'].show();
                break;
            default:
                this.getFields()['alert_title'].hide();
                this.getFields()['trigger_value'].hide();
                break;
        }
    }
});
