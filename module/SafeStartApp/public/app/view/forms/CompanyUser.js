Ext.define('SafeStartApp.view.forms.CompanyUser', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartCompanyUserForm',
    config: {
        height: 640,
        maxWidth: 600,
        scrollable: false,
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
                        xtype: 'emailfield',
                        label: 'Email',
                        required: true,
                        name: 'email'
                    },
                    {
                        xtype: 'textfield',
                        label: 'First name',
                        required: true,
                        name: 'firstName'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Last name',
                        required: true,
                        name: 'lastName'
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
                            this.up('SafeStartCompanyUserForm').fireEvent('delete-data', this.up('SafeStartCompanyUserForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Reset',
                        name: 'reset-data',
                        handler: function() {
                            this.up('SafeStartCompanyUserForm').fireEvent('reset-data', this.up('SafeStartCompanyUserForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Send Credentials',
                        name: 'send-credentials',
                        ui: 'action',
                        handler: function() {
                            this.up('SafeStartCompanyUserForm').fireEvent('send-credentials', this.up('SafeStartCompanyUserForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartCompanyUserForm').fireEvent('save-data', this.up('SafeStartCompanyUserForm'));
                        }
                    }
                ]
            }
        ]
    }
});
