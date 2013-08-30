Ext.define('SafeStartApp.view.forms.Contact', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartContactForm',
    config: {
        autoRender: true,
        floating: false,
        centered: true,
        cls: 'sfa-auth-form',
        height: 570,
        width: 450,
        items: [
            {
                xtype: 'fieldset',
                title: 'Contact Us',
                //instructions: 'Email address is optional',

                items: [
                    {
                        xtype: 'textfield',
                        label: 'Name',
                        name: 'name'
                    },
                    {
                        xtype: 'emailfield',
                        label: 'Email',
                        name: 'email'
                    },
                    {
                        xtype: 'textareafield',
                        label: 'Message',
                        name: 'message'
                        //height: 90
                    }
                ]
            },
            {
                xtype: 'button',
                text: 'Send',
                ui: 'confirm',
                handler: function() {
                    SafeStartApp.showInfoMsg('Coming soon');
                }
            }
        ]
    }

});
