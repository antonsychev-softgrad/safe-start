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
                layout: 'absolute',
                xtype: 'container',
                items: [{
                    xtype: 'image',
                    src: '/resources/img/logo-small.png',
                    width: 381,
                    y: -104,
                    cls: 'logo'
                }]
            }, {
                xtype: 'form',
                ui: 'form',
                width: 480,
                height: 372,
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
                        var form = this.down('form');
                        if (form.getForm().isValid()) {
                            this.fireEvent('loginAction', form.getForm().getValues());
                        }
                    },
                    scope: this
                }],
                items: [{
                    xtype: 'textfield',
                    height: 56,
                    labelAlign: 'top',
                    name: 'username',
                    allowBlank: false,
                    fieldLabel: 'Username'
                }, {
                    xtype: 'textfield',
                    inputType: 'password',
                    height: 56,
                    labelAlign: 'top',
                    name: 'password',
                    allowBlank: false,
                    fieldLabel: 'Password'
                }, {
                    xtype: 'container',

                    cls: 'sfa-text-info',
                    html: 'If you do not have password please contact us'
                }]
            }]
        });
        this.callParent();
    }
});

