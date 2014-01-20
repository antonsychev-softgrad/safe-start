Ext.define('SafeStartApp.view.forms.Auth', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartAuthForm',
    config: {
        autoRender: true,
        floating: false,
        centered: true,
        cls: 'sfa-auth-form',
        height: 398,
        width: 480,
        scrollable: false
    },

    initialize: function () {
        var me = this;
        this.setItems([{
                xtype: 'fieldset',
                title: 'Login',
                instructions: 'If you do not have password please contact us',
                items: [{
                        name: 'username',
                        xtype: 'textfield',
                        label: 'Username',
                        required: true,
                        allowBlank: false
                    }, {
                        name: 'password',
                        xtype: 'passwordfield',
                        label: 'Password',
                        required: true,
                        allowBlank: false
                    }]
            }, {
                xtype: 'component',
                cls: 'sfa-text-forgot-password',
                height: 30,
                html: 'Forgot password?',
                listeners: {
                    tap: function (c) {
                        me.showForgotPasswordDialog();
                    },
                    element: 'element'
                }
            }, {
                xtype: 'button',
                text: 'Sign In',
                action: 'login',
                ui: 'confirm'
            }
        ]);
        this.callParent(arguments);
    },

    showForgotPasswordDialog: function() {
        var dialog = SafeStartApp.view.abstract.dialog.create({
            title: 'Enter your email',
            padding: 10,
            width: 400,
            height: 150,
            items: [{
                xtype: 'titlebar',
                title: 'Enter your email'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Email',
                name: 'email',
                vtype: 'email'
            }, {
                margin: '10 0 0 0',
                width: 50,
                xtype: 'button',
                text: 'Sent',
                handler: function() {
                    var email = dialog.down('textfield[name=email]').getValue();
                    if (! email) {
                        return;
                    }
                    dialog.hide();

                    SafeStartApp.AJAX('user/forgotpassword', {email: email}, function (result) {
                        if (!result.done) {
                            dialog.show();
                            return;
                        }
                        dialog.destroy();
                        Ext.Msg.alert('Recovery password', 'Message that contains link to reset password was sent to your email');
                    });
                }
            }]
        });
        this.up('component').add(dialog);
    }

});