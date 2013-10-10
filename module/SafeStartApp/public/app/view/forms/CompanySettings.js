Ext.define('SafeStartApp.view.forms.CompanySettings', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartCompanySettingsForm',
    config: {
        minHeight: 400,
        //maxWidth: 600,
        cls: 'comp-settings',
        items: [
            {
                xtype: 'fieldset',
                title: 'Company Settings',
                items: [
                    {
                        xtype: 'hiddenfield',
                        name: 'id'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Company Name',
                        required: true,
                        name: 'title'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Responsible Name',
                        required: true,
                        name: 'firstName'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Responsible Email',
                        required: true,
                        name: 'email'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Company Address',
                        name: 'address'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Company Phone',
                        name: 'phone'
                    },
                    {
                        xtype: 'textareafield',
                        label: 'Company Info',
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
                        cls: 'subscription-fieldset',
                        items: [
                            {
                                xtype: 'spinnerfield',
                                maxValue: 1000,
                                minValue: 1,
                                stepValue: 1,
                                name: 'max_users',
                                required: true,
                                label: 'Number of users'
                            },
                            {
                                xtype: 'spinnerfield',
                                maxValue: 1000,
                                minValue: 1,
                                stepValue: 1,
                                name: 'max_vehicles',
                                required: true,
                                label: 'Number of vehicles'
                            },
                            {
                                xtype: 'datepickerfield',
                                name: 'expiry_date',
                                required: true,
                                label: 'Expiry Date',
                                dateFormat: SafeStartApp.dateFormat,
                                value: new Date(),
                                cls: 'sfa-datepicker',
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
                xtype: 'toolbar',
                docked: 'bottom',
                items: [
                    {
                        xtype: 'button',
                        name: 'delete-data',
                        text: 'Delete',
                        ui: 'decline',
                        iconCls: 'delete',
                        handler: function() {
                            this.up('SafeStartCompanySettingsForm').fireEvent('delete-data', this.up('SafeStartCompanySettingsForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        name: 'manage',
                        text: 'Manage',
                        ui: 'action',
                        iconCls: 'compose',
                        handler: function() {
                            this.up('SafeStartCompanySettingsForm').fireEvent('manage', this.up('SafeStartCompanySettingsForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Reset',
                        name: 'reset-data',
                        handler: function() {
                            this.up('SafeStartCompanySettingsForm').fireEvent('reset-data', this.up('SafeStartCompanySettingsForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Send Credentials',
                        name: 'send-credentials',
                        ui: 'action',
                        handler: function() {
                            this.up('SafeStartCompanySettingsForm').fireEvent('send-credentials', this.up('SafeStartCompanySettingsForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartCompanySettingsForm').fireEvent('save-data', this.up('SafeStartCompanySettingsForm'));
                        }
                    }
                ]
            }
        ]
    }
});
