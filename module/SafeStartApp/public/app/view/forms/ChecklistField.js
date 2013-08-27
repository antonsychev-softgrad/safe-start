Ext.define('SafeStartApp.view.forms.ChecklistField', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartChecklistFieldForm',
    config: {
        minHeight: 400,
        maxWidth: 600,
        scrollable: false,
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
                }
            },
            {
                xtype: 'togglefield',
                name: 'additional',
                label: 'Hide sub questions',
                listeners: {
                    change: function (field, slider, thumb, newValue, oldValue) {

                    }
                }
            },
            {
                xtype: 'selectfield',
                name: 'trigger_value',
                label: 'Alert or additional <br/> questions trigger value',
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
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
                label: 'Enabled',
                listeners: {
                    change: function (field, slider, thumb, newValue, oldValue) {

                    }
                }
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
            change: function(form, record, xz, eOpts) {
                if(!record.get('parentId')) {
                    form.showCreateRootCategory();
                } else {
                    form.showCreateFieldCategory();
                }
            }
        }
    },

    showCreateRootCategory: function() {
        this.getFields()['type'].setValue('root');
        this.getFields()['type'].hide();
        this.getFields()['alert_title'].hide();
        this.getFields()['additional'].setLabel('Additional');
        this.getFields()['trigger_value'].hide();
        this.getFields()['trigger_value'].setValue('yes');
    },

    showCreateFieldCategory: function() {

    }
});
