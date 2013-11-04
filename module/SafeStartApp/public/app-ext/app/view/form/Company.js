Ext.define('SafeStartExt.view.form.Company', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Text',
        'Ext.form.field.Hidden',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormCompany',

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            buttons: [{
                text: 'Delete',
                name: 'delete-data',
                ui: 'red',
                scale: 'medium',
                handler: function () {
                    Ext.Msg.confirm({
                        title: 'Confirmation',
                        msg: 'Are you sure want to delete this company?',
                        buttons: Ext.Msg.YESNO,
                        fn: function (btn) {
                            if (btn !== 'yes') {
                                return;
                            }
                            me.fireEvent('deleteCompanyAction', me.getRecord());
                        }
                    });
                }
            },
            {
                text: 'Save',
                ui: 'green',
                name: 'save-data',
                scale: 'medium',
                handler: function () {
                    if (me.isValid()) {
                        me.fireEvent('updateCompanyAction', me.getRecord(), me.getValues());
                    }
                }
            },
            {
                text: 'Send Password to Company Owner',
                ui: 'blue',
                name: 'send-password',
                scale: 'medium',
                handler: function () {
                    if (me.isValid()) {
                        me.fireEvent('sendPasswordAction', me.getRecord());
                    }
                }
            },
            {
                text: 'Manage',
                ui: 'blue',
                name: 'manage-data',
                scale: 'medium',
                handler: function () {
                    if (me.isValid()) {
                        me.fireEvent('manageCompanyAction', me.getRecord());
                    }
                }
            }],
            items: [
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Company Settings',
                    labelAlign: 'top',
                    items: [
                        {
                            xtype: 'hiddenfield',
                            name: 'id'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Company Name',
                            allowBlank: false,
                            name: 'title'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Responsible Name',
                            allowBlank: false,
                            name: 'firstName'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Responsible Email',
                            vtype: 'email',
                            allowBlank: false,
                            name: 'email'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Company Address',
                            name: 'address'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Company Phone',
                            name: 'phone'
                        },
                        {
                            xtype: 'textareafield',
                            fieldLabel: 'Company Info',
                            name: 'description'
                        },
                        {
                            xtype: 'checkboxfield',
                            name: 'restricted',
                            fieldLabel: 'Limited Access',
                            cls:'sfa-limited-access',
                            handler: function() {
                                if (this.checked) {
                                    this.up('form').down('[name=subscription]').enable();
                                } else {
                                    this.up('form').down('[name=subscription]').disable();
                                }
                            }
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Subscription',
                    name: 'subscription',
                    labelAlign: 'top',
                    items: [
                        {
                            xtype: 'numberfield',
                            maxValue: 1000,
                            minValue: 1,
                            stepValue: 1,
                            value: 1,
                            name: 'max_users',
                            required: true,
                            fieldLabel: 'Number of users'
                        },
                        {
                            xtype: 'numberfield',
                            maxValue: 1000,
                            minValue: 1,
                            value: 1,
                            stepValue: 1,
                            name: 'max_vehicles',
                            required: true,
                            fieldLabel: 'Number of vehicles'
                        },
                        {
                            xtype: 'datefield',
                            name: 'expiry_date',
                            required: true,
                            altFormats: 'U',
                            format: SafeStartExt.dateFormat,
                            fieldLabel: 'Expiry Date',
                            value: new Date(),
                            cls: 'sfa-datepicker'
                        }
                    ]
                }
            ]
        });
        this.callParent();
    }
});
