Ext.define('SafeStartExt.view.component.Auth', {
    extend: 'Ext.container.Container',
    requires: [
    ],
    xtype: 'SafeStartExtComponentAuth',

    layout: {
        type: 'vbox',
        pack: 'center',
        align: 'center'
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'form',
                ui: 'form',
                width: 450,
                height: 360,
                title: 'Login',
                defaults: {
                    labelAlign: 'top',
                    margin: '0 0 20 0',
                    width: '100%'
                },
                buttonAlign: 'left',
                buttons: [{
                    text: 'Sign In',
                    scale: 'large',
                    handler: function () {
                    },
                    scope: this
                }],
                items: [{
                    xtype: 'textfield',
                    height: 56,
                    labelAlign: 'top',
                    allowBlank: false,
                    fieldLabel: 'Username'
                }, {
                    xtype: 'textfield',
                    inputType: 'password',
                    height: 56,
                    labelAlign: 'top',
                    allowBlank: false,
                    vtype: 'email',
                    fieldLabel: 'Password'
                }]
            }]
        });
        this.callParent();
    }
});

