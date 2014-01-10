Ext.define('SafeStartExt.view.component.Auth', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.layout.container.Absolute'
    ],
    xtype: 'SafeStartExtComponentAuth',
    cls:'sfa-form-auth',
    layout: {
        type: 'vbox',
        pack: 'center',
        align: 'center'
    },

    initComponent: function () {
        var me = this;
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
                    ui: 'green',
                    scale: 'medium',
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
                    cls: 'sfa-text-forgot-password',
                    autoEl: { 
                        tag: 'div',
                        'class': 'sfa-forgot-password',
                        html: 'Forgot password?' 
                    },
                    listeners: {
                        render: function(c){
                            c.getEl().on({
                                click: function() {
                                    me.showForgotPasswordDialog();
                                }
                            });
                        }
                    }                
                }, {
                    xtype: 'container',
                    cls: 'sfa-text-info',
                    html: 'If you do not have password please contact us'
                }]
            }]
        });
        this.callParent();
    },
    showForgotPasswordDialog: function () {
        var dialog = Ext.window.Window.create({
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Email',
                name: 'email',
                vtype: 'email'
            }, {
                xtype: 'button',
                text: 'Send',
                handler: function () {
                    var win = this.up('window');
                    var email = win.down('textfield[name=email]').getValue();
                    if (email) {
                        SafeStartExt.Ajax.request({
                            url: 'user/forgotpassword',
                            data: {
                                email: email
                            },
                            success: function (result) {
                                if (! result.done) {
                                    win.close();
                                    return;
                                }
                            }
                        });
                    }
                }
            }]
        });
        dialog.show();
    }
});

