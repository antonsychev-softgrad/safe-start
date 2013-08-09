Ext.define('SafeStartApp.controller.Auth', {
    extend: 'Ext.app.Controller',
    require: [
        //models
        'SafeStartApp.model.UserAuth',
        // dialogs
        'SafeStartApp.view.dialogs.UserProfile'
    ],

    config: {
        control: {
            loginButton: {
                tap: 'loginAction'
            },
            logoutButton: {
                tap: 'logoutAction'
            },
            showProfileDlgButton: {
                tap: 'showProfileDlgAction'
            }
        },

        refs: {
            loginButton: 'SafeStartAuthForm > button[action=login]',
            logoutButton: 'SafeStartMainToolbar > button[action=logout]',
            loginForm: 'SafeStartAuthForm',
            showProfileDlgButton: 'SafeStartMainToolbar > button[action=update_profile]'
        }
    },

    loginAction: function () {
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
    },

    logoutAction: function() {
        SafeStartApp.AJAX('user/logout', {}, function (result) {
            SafeStartApp.currentUser = result.userInfo;
            SafeStartApp.loadMainMenu();
        });
    },

    showProfileDlgAction: function() {
        if (!this.profileDlg) {
            this.profileDlg = Ext.Viewport.add(Ext.create('SafeStartApp.view.dialogs.UserProfile'))
        }
        this.profileDlg.show();
    },

    updateProfileAction: function() {

    }
});