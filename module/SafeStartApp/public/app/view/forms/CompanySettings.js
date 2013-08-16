Ext.define('SafeStartApp.view.forms.CompanySettings', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartCompanySettingsForm',
    config: {
        height: 810,
        maxWidth: 600,
        scrollable: false,
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
                        cls: 'sfa-company-setting-subscription',
                        items: [
                            {
                                xtype: 'spinnerfield',
                                maxValue: 1000,
                                minValue: 1,
                                increment: 1,
                                name: 'max_users',
                                required: true,
                                label: 'Number of users'
                            },
                            {
                                xtype: 'spinnerfield',
                                maxValue: 1000,
                                minValue: 1,
                                increment: 1,
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
