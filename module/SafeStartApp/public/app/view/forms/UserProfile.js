Ext.define('SafeStartApp.view.forms.UserProfile', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartUserProfileForm',
    config: {
        height: 450,
        items: [
            {
                xtype: 'fieldset',
                title: 'Personal Info',
                instructions: 'You chan change your info above.',
                items: [
                    {
                        xtype: 'textfield',
                        label: 'First Name',
                        name: 'firstName'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Last Name',
                        name: 'lastName'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Email',
                        name: 'email'
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Change password:',
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'new_password',
                                label: 'New'
                            },
                            {
                                xtype: 'textfield',
                                name: 'confirm_password',
                                label: 'Confirm'
                            }
                        ]
                    }
                ]
            }
        ]
    }
});
