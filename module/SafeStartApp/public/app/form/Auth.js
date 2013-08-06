Ext.define('SafeStartApp.form.Auth', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartAuthForm',
    config: {
        autoRender: true,
        floating: false,
        centered: true,
        cls: 'sfa-auth-form',
        height: 385,
        width: 480,
        scrollable: false,

        items: [
            {
                html: [
                    '<img height=100 src="/logo.png" />'
                ].join("")
            },
            {
                xtype: 'fieldset',
                title: 'Auth',
                instructions: 'If you do not have password please contact us',
                items: [

                    {
                        name: 'username',
                        xtype: 'textfield',
                        label: 'Name',
                        required: true,
                        allowBlank:false
                    },
                    {
                        name: 'password',
                        xtype: 'passwordfield',
                        label: 'Password',
                        required: true,
                        allowBlank:false
                    }
                ]
            },
            {
                xtype: 'button',
                text: 'Sign In',
                action: 'login',
                width: 100,
                ui: 'confirm'
            }
        ]
    }

});
