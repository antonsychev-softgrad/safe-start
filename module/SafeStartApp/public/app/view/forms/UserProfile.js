Ext.define('SafeStartApp.view.forms.UserProfile', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartUserProfileForm',
    config: {
        height: 300,
        scrollable: false,
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
                    }
                ]
            }
        ]
    }
});
