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
                xtype: 'container',
                height: 60
            }, {
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
                //autoEl: {
                //    tag: 'form',
                //    method: 'post',
                //    action: ''
                //},
                buttonAlign: 'left',
                buttons: [{
                    text: 'Sign In',
                    ui: 'green',
                    scale: 'medium',
                    name: 'sign-in',
                    xtype: 'button',
                    type: 'submit',
                    preventDefault: false,
                    handler: function (btn) {
                        var form = btn.up('form');
                        if (form.getForm().isValid()) {
                            //form.url = SafeStartExt.Ajax.baseHref + 'user/login';
                            //form.submit({
                            //    success: function(f, a) {},
                            //    failure: function(f, a) {}
                            //});
                            form.up('SafeStartExtComponentAuth').fireEvent('loginAction', form.getForm().getValues());
                        }
                    },
                    scope: this
                }/*, {
                    xtype: 'checkboxfield',
                    name: 'remember',
                    boxLabel: 'Remember Me',
                    id: 'remember',
                    inputValue: '1',
                    uncheckedValue: '0',
                    checked: false,
                    boxLabelAlign: 'before',
                    cls: 'sfa-auth-field-remember'
                }*/],
                items: [{
                    xtype: 'textfield',
                    height: 56,
                    labelAlign: 'top',
                    name: 'username',
                    inputId: 'username',
                    allowBlank: false,
                    fieldLabel: 'Username',
                    enableKeyEvents: true,
                    listeners: {
                        keyup: function(textfield, eventObject){
                            var btn = this.up('form').down('button[name=sign-in]');
                            if (eventObject.getKey() == Ext.EventObject.ENTER) {
                                btn.handler(btn);
                            }
                        },
                        afterrender:function(cmp){
                            cmp.inputEl.set({
                                autocomplete:'on'
                            });
                        }
                    }
                }, {
                    xtype: 'textfield',
                    inputType: 'password',
                    height: 56,
                    labelAlign: 'top',
                    name: 'password',
                    inputId: 'password',
                    allowBlank: false,
                    fieldLabel: 'Password',
                    enableKeyEvents: true,
                    listeners: {
                        keyup: function(textfield, eventObject){
                            var btn = this.up('form').down('button[name=sign-in]');
                            if (eventObject.getKey() == Ext.EventObject.ENTER) {
                                btn.handler(btn);
                            }
                        },
                        afterrender:function(cmp){
                            cmp.inputEl.set({
                                autocomplete:'on'
                            });
                        }
                    }
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
            title: 'Enter your email',
            padding: 10,
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Email',
                name: 'email',
                vtype: 'email'
            }, {
                margin: '10 0 0 0',
                xtype: 'button',
                text: 'Send',
                handler: function () {
                    var win = this.up('window');
                    var email = win.down('textfield[name=email]').getValue();
                    if (email) {
                        win.hide();
                        SafeStartExt.Ajax.request({
                            url: 'user/forgotpassword',
                            data: {
                                email: email
                            },
                            success: function (result) {
                                if (! result.done) {
                                    win.show();
                                    return;
                                }
                                win.close();
                                Ext.Msg.alert({
                                    title: 'Recovery password',
                                    width: 200,
                                    msg: 'Message that contains link to reset password was sent to your email',
                                    buttons: Ext.Msg.OK
                                });
                            }
                        });
                    }
                }
            }]
        });
        dialog.show();
    }
});

