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

    padding: 10,

    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    fieldDefaults: {
        msgTarget: 'side'
    },
    defaults: {
        maxWidth: 400,
        labelWidth: 130 
    },

    items: [{
            xtype: 'hiddenfield',
            name: 'id',
            value: 0
        }, {
            xtype: 'textfield',
            fieldLabel: 'Email',
            allowBlank: false,
            vtype: 'email',
            name: 'email'
        }, {
            xtype: 'textfield',
            fieldLabel: 'First name',
            allowBlank: false,
            name: 'firstName'
        }, {
            xtype: 'textfield',
            fieldLabel: 'Last name',
            allowBlank: false,
            name: 'lastName'
        }, {
            xtype: 'textfield',
            fieldLabel: 'Position',
            name: 'position'
        }, {
            xtype: 'textfield',
            fieldLabel: 'Sector/Department',
            name: 'department'
        }, {
            xtype: 'combobox',
            editable: false,
            fieldLabel: 'Company role',
            cls:'sfa-combobox',
            name: 'role',
            queryMode: 'local',
            displayField: 'title',
            valueField: 'rank',
            allowBlank: false,
            listeners: {
                change: function (combo, value) {
                    if (combo.up('form').getRecord().get('role') == 'companyAdmin') {
                        combo.setValue('Admin');
                        combo.setDisabled(true);
                        combo.up('form').down("checkboxfield[name=enabled]").setDisabled(true);
                    } else {
                        combo.setDisabled(false);
                        combo.up('form').down("checkboxfield[name=enabled]").setDisabled(false);
                    }
                }
            },
            store: Ext.create('Ext.data.Store', {
                fields: ['rank', 'title'],
                data: [{
                    rank: 'companyManager',
                    title: 'Manager'
                }, {
                    rank: 'companyUser',
                    title: 'User'
                }]
            })
        }, {
            xtype: 'checkboxfield',
            name: 'enabled',
            fieldLabel: 'Enabled',
            listeners: {
                change: function(field, slider, thumb, newValue, oldValue) {

                }
            }
        }

    ],

    bbar: [{
        xtype: 'container',
        defaults: {
            margin: '4 8'
        },
        items: [{
            xtype: 'button',
            text: 'Delete',
            minWidth: 140,
            name: 'delete-data',
            ui: 'red',
            scale: 'medium',
            handler: function() {
                var self = this;
                Ext.Msg.confirm({
                    title: 'Confirmation',
                    msg: 'Are you sure want to delete this user?',
                    buttons: Ext.Msg.YESNO,
                    fn: function(btn) {
                        if (btn !== 'yes') {
                            return;
                        }
                        self.up('form').fireEvent('deleteUserAction');
                    }
                });
            }
        }, {
            xtype: 'button',
            text: 'Save',
            ui: 'blue',
            name: 'save-data',
            minWidth: 140,
            scale: 'medium',
            handler: function() {
                this.up('form').fireEvent('updateUserAction');
            }
        }, {
            xtype: 'button',
            text: 'Send Password to User',
            ui: 'transparent',
            name: 'send-password',
            minWidth: 140,
            scale: 'medium',
            handler: function() {
                this.up('form').fireEvent('sendPasswordAction');
            }
        }]

    }]
});
