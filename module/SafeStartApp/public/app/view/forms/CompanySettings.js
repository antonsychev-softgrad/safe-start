Ext.define('SafeStartApp.view.forms.CompanySettings', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartCompanySettingsForm',
    config: {
        height: 800,
        maxWidth: 600,
        scrollable: false,
        items: [
            {
                xtype: 'fieldset',
                title: 'Company Settings',
                instructions: 'You chan change company settings info above.',
                items: [
                    {
                        xtype: 'hiddenfield',
                        name: 'id'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Title',
                        required: true,
                        name: 'title'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Responsible person email',
                        required: true,
                        name: 'email'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Responsible person name',
                        required: true,
                        name: 'firstName'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Adress',
                        name: 'address'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Phone',
                        name: 'phone'
                    },
                    {
                        xtype: 'textareafield',
                        label: 'Info',
                        name: 'description'
                    },
                    {
                        xtype: 'togglefield',
                        name: 'restricted',
                        label: 'Limited Access',
                        listeners: {
                            change: function(field, slider, thumb, newValue, oldValue) {
                                if (newValue) {
                                    this.up('SafeStartCompanySettingsForm').down('fieldset').down('fieldset').enable();
                                } else {
                                    this.up('SafeStartCompanySettingsForm').down('fieldset').down('fieldset').disable();
                                }
                            }
                        }
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Subscription:',
                        id: 'subscription',
                        items: [
                            {
                                xtype: 'numberfield',
                                value: 0,
                                maxValue: 99,
                                minValue: 0,
                                name: 'max_users',
                                required: true,
                                label: 'Number of users'
                            },
                            {
                                xtype: 'numberfield',
                                value: 0,
                                maxValue: 99,
                                minValue: 0,
                                name: 'max_vehicles',
                                required: true,
                                label: 'Number of vehicles'
                            },
                            {
                                xtype: 'datepickerfield',
                                name: 'expiry_date',
                                required: true,
                                label: 'Expiry Date',
                                value: new Date(),
                                picker: {
                                    yearFrom: new Date().getFullYear(),
                                    yearTo: new Date().getFullYear() + 10
                                }
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'button',
                text: 'Save',
                action: 'save-company',
                ui: 'confirm',
                handler: function() {
                    this.up('SafeStartCompanySettingsForm').fireEvent('save-data', this.up('SafeStartCompanySettingsForm'));
                }
            }
        ]
    }
});
