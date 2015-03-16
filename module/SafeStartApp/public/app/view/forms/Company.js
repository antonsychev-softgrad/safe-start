Ext.define('SafeStartApp.view.forms.Company', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartCompanyForm',
    config: {
        minHeight: 400,
        //maxWidth: 600,
        cls: 'comp-settings',
        items: [
            {
                xtype: 'fieldset',
                title: 'Company Settings',
                cls:'sfa-company-settings',
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
                        disabled: true,
                        name: 'firstName'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Responsible Email',
                        required: true,
                        listeners: {
                            keyup: function (view) {
                                var form = view.up('SafeStartCompanyForm'),
                                    firstNameView = form.down('textfield[name=firstName]'),
                                    value = view.getValue(),
                                    originalValue = view.originalValue;

                                if (value && value == originalValue) {
                                    firstNameView.disable();
                                } else {
                                    firstNameView.enable();
                                }
                            }
                        },
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
                        cls:'sfa-limited-access',
                        listeners: {
                            change: function(field, slider, thumb, newValue, oldValue) {
                                if (this.up('SafeStartCompanyForm').down('fieldset').down('fieldset').isDisabled() == false) {   // isEnabled
                                    this.up('SafeStartCompanyForm').down('fieldset').down('fieldset').disable();
                                }
                                else {
                                    this.up('SafeStartCompanyForm').down('fieldset').down('fieldset').enable();
                                    if(this.up('SafeStartCompanyForm').down('[name=unlim_expiry_date]').getValue() == true){
                                        this.up('SafeStartCompanyForm').down('fieldset').down('[name=expiry_date]').disable();
                                    } else {
                                        this.up('SafeStartCompanyForm').down('fieldset').down('[name=expiry_date]').enable();
                                    }
                                }
                            }
                        }
                    },
                    {
                        xtype: 'togglefield',
                        name: 'unlim_expiry_date',
                        label: 'Unlimited Expiry Date',
                        cls: 'sfa-limited-access',
                        listeners: {
                            change: function(field, slider, thumb, newValue, oldValue) {
                                if(this.up('SafeStartCompanyForm').down('fieldset').down('fieldset').isDisabled() == false){
                                if (this.up('SafeStartCompanyForm').down('fieldset').down('[name=expiry_date]').isDisabled() == false) {   // isEnabled
                                    this.up('SafeStartCompanyForm').down('fieldset').down('[name=expiry_date]').disable();
                                }
                                else {
                                    this.up('SafeStartCompanyForm').down('fieldset').down('[name=expiry_date]').enable();                                 // enable togglefield
                                }
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
                                cls:'sfa-number-of',
                                stepValue: 1,
                                name: 'max_users',
                                required: true,
                                label: 'Number of users'
                            },
                            {
                                xtype: 'spinnerfield',
                                maxValue: 1000,
                                minValue: 1,
                                cls:'sfa-number-of',
                                stepValue: 1,
                                name: 'max_vehicles',
                                required: true,
                                label: 'Number of vehicles'
                            },
                            {
                                xtype: 'datepickerfield',
                                name: 'expiry_date',
                                required: true,
                                label: 'Subscription Expiry',
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
                            this.up('SafeStartCompanyForm').fireEvent('delete-data', this.up('SafeStartCompanyForm'));
                        }
                    },

                    {
                        xtype: 'button',
                        name: 'manage',
                        text: 'Manage',
                        ui: 'action',
                        iconCls: 'compose',
                        handler: function() {
                            this.up('SafeStartCompanyForm').fireEvent('manage', this.up('SafeStartCompanyForm'));
                        }
                    },

                    {
                        xtype: 'button',
                        text: 'Reset',
                        name: 'reset-data',
                        handler: function() {
                            this.up('SafeStartCompanyForm').fireEvent('reset-data', this.up('SafeStartCompanyForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartCompanyForm').fireEvent('save-data', this.up('SafeStartCompanyForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        cls:'sfa-last',
                        text: 'Send Password to Company Owner',
                        name: 'send-credentials',
                        ui: 'action',
                        handler: function() {
                            this.up('SafeStartCompanyForm').fireEvent('send-credentials', this.up('SafeStartCompanyForm'));
                        }
                    }

                ]
            }
        ]
    },

    setRecord: function (record) {
        if (! record || typeof record.get != 'function') {
            this.callParent([record]);
            return;
        }
        if (record.get('email')) {
            this.down('textfield[name=firstName]').disable();
        }
        this.down('emailfield[name=email]').originalValue = record.get('email');
        this.callParent([record]);
    }
});
