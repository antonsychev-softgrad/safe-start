Ext.define('SafeStartApp.form.Auth', {

    extend: 'Ext.Container',
    xtype: 'SafeStartAuthForm',

    config: {
        layout: 'fit',
        items: [
            {
                xtype: 'fieldset',
                title: 'Auth ',
                instructions: 'If you do not have password',
                items: [
                    {
                        xtype: 'textfield',
                        label: 'Name'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Email'
                    }
                ]
            },
            {
                xtype: 'button',
                text: 'Sign In',
                ui: 'confirm'
            }
        ]
    }

});
