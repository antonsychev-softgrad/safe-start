Ext.define('SafeStartApp.view.forms.UserProfile', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartUserProfileForm',
    config: {
        height: 430,
        scrollable: false,
        items: [
            {
                xtype: 'fieldset',
                title: 'Personal Info',
            /*    instructions: 'You can change your info above.',*/
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
                                name: 'newPassword',
                                label: 'New'
                            },
                            {
                                xtype: 'textfield',
                                name: 'confirmPassword',
                                label: 'Confirm'
                            }
                        ]
                    }
                ]
            }
        ]
    }
});
