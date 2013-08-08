Ext.define('SafeStartApp.controller.Auth', {
    extend: 'Ext.app.Controller',
    require: [
        'SafeStartApp.model.UserAuth'
    ],

    config: {
        control: {
            loginButton: {
                tap: 'doLogin'
            }
        },

        refs: {
            loginButton: 'SafeStartAuthForm > button[action=login]',
            loginForm: 'SafeStartAuthForm'
        }

    },

    doLogin: function () {
        var self = this;
        var validateMessage = "";
        var formFields = this.getLoginForm().getFields();
        var model = Ext.create('SafeStartApp.model.UserAuth');
        model.setData(this.getLoginForm().getValues());
        var errors = model.validate();
        Ext.iterate(formFields, function (key, val) {
            if (errors.getByField(key)[0]) {
                validateMessage += errors.getByField(key)[0].getMessage() + "<br>";
                val.addCls('x-invalid');
            } else {
                val.removeCls('x-invalid');
            }
        });
        if (errors.isValid()) {
            SafeStartApp.AJAX('user/login', this.getLoginForm().getValues(), function (result) {
                SafeStartApp.currentUser = result.userInfo;
                SafeStartApp.loadMainMenu();
            });
        } else {
            Ext.Msg.alert("Please fill required fields.", validateMessage, Ext.emptyFn());
            return false;
        }
    }
});