Ext.define('SafeStartExt.view.form.User', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.ComboBox',
        'Ext.form.field.Text',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormUser',

    items: [
        {
            xtype: 'hiddenfield',
            name: 'id',
            value: 0
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Email',
            allowBlank: false,
            vtype: 'email',
            name: 'email'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'First name',
            allowBlank: false,
            name: 'firstName'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Last name',
            allowBlank: false,
            name: 'lastName'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Position',
            name: 'position'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Sector/Department',
            name: 'department'
        },
        {
            xtype: 'combobox',
            fieldLabel: 'Company role',
            name: 'role',
            queryMode: 'local',
            displayField: 'title',
            valueField: 'rank',
            allowBlank: false,
            store: Ext.create('Ext.data.Store', {
                fields: ['rank', 'title'],
                data : [
                    { rank: 'companyManager', title: 'Manager'},
                    { rank: 'companyUser', title: 'User'}
                ]
            })
        },
        {
            xtype: 'checkboxfield',
            name: 'enabled',
            fieldLabel: 'Enabled',
            listeners: {
                change: function(field, slider, thumb, newValue, oldValue) {

                }
            }
        }

    ],

    buttons: [
        {
            text: 'Delete',
            name: 'delete-data',
            ui: 'red',
            scale: 'medium',
            handler: function () {
                var self = this;
                Ext.Msg.confirm({
                    title: 'Confirmation',
                    msg: 'Are you sure want to delete this user?',
                    buttons: Ext.Msg.YESNO,
                    fn: function (btn) {
                        if (btn !== 'yes') {
                            return;
                        }
                        self.up('form').fireEvent('deleteUserAction');
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
                this.up('form').fireEvent('updateUserAction');
            }
        },
        {
            text: 'Send Password to User',
            ui: 'blue',
            name: 'send-password',
            scale: 'medium',
            handler: function () {
                this.up('form').fireEvent('sendPasswordAction');
            }
        },
    ]
});
