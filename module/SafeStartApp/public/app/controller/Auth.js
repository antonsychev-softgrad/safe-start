Ext.define('SafeStartApp.controller.Auth', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],
    requires: [
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
            },
            updateProfileButton: {
                tap: 'updateProfileAction'
            }
        },

        refs: {
            loginButton: 'SafeStartAuthForm > button[action=login]',
            logoutButton: 'SafeStartMainToolbar > button[action=logout]',
            loginForm: 'SafeStartAuthForm',
            showProfileDlgButton: 'SafeStartMainToolbar > button[action=update_profile]',
            updateProfileForm: 'SafeStartUserProfileForm',
            updateProfileButton: 'SafeStartUserProfileDialog > button[action=save-data]'
        }
    },

    loginAction: function () {
        if (!this.userAuthModel)this.userAuthModel = Ext.create('SafeStartApp.model.UserAuth');
        if (this.validateFormByModel(this.userAuthModel, this.getLoginForm())) {
            SafeStartApp.AJAX('user/login', this.getLoginForm().getValues(), function (result) {
                SafeStartApp.loadMainMenu();
            });
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
            this.profileDlg = Ext.Viewport.add(Ext.create('SafeStartApp.view.dialogs.UserProfile'));
            this.profileDlg.addListener('save-data', this.updateProfileAction, this);
        }
        this.profileDlg.show();
    },

    updateProfileAction: function(dlg, e) {
        var self = this;
        if (!this.userProfileModel)this.userProfileModel = Ext.create('SafeStartApp.model.User');
        if (this.validateFormByModel(this.userProfileModel, this.getUpdateProfileForm())) {
            SafeStartApp.AJAX('user/'+SafeStartApp.userModel.get('id')+'/profile/update', this.getUpdateProfileForm().getValues(), function (result) {
                Ext.iterate(self.getUpdateProfileForm().getFields(), function (key, item) { SafeStartApp.userModel.set(key, item.getValue()); }, this);
                self.getShowProfileDlgButton().setText(SafeStartApp.userModel.get('firstName') +' '+ SafeStartApp.userModel.get('lastName'));
                dlg.hide();
            });
        }
    }
});