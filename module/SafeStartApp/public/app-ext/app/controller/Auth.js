Ext.define('SafeStartExt.controller.Auth', {
    extend: 'Ext.app.Controller',
    requires: [
        'SafeStartExt.view.panel.UserProfile'
    ],
    refs: [{
        selector: 'viewport',
        ref: 'viewport'
    }, {
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'bottomNavPanel'
    }, {
        selector: 'viewport > SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentAuth',
        ref: 'authPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentCompany',
        ref: 'companyPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentContact',
        ref: 'contactPanel'
    }, {
        selector: 'viewport > SafeStartExtUserProfileWindow > button[action=save-data]',
        ref: 'updateProfileButton'
    }, {
        selector: 'viewport > SafeStartExtUserProfileWindow > SafeStartExtUserProfileForm',
        ref: 'updateProfileForm'
    }, {
        selector: 'SafeStartExtContainerTopNav',
        ref: 'topNav'
    }],

    init: function() {
        //todo: hack for mac, have to reload scrolls for auth form
        document.documentElement.style.overflow = 'auto';
        this.callParent();
        this.control({
            'SafeStartExtComponentAuth': {
                loginAction: this.loginAction
            },
            'SafeStartExtContainerTopNav': {
                logoutAction: this.logoutAction,
                showProfileAction: this.showProfileAction
            },
            'updateProfileButton': {
                updateProfileAction: 'updateProfileAction'
            }
        });
    },

    loginAction: function(data) {
        var viewport = this.getViewport();
        SafeStartExt.Ajax.request({
            url: 'user/login',
            data: data,
            success: function() {
                viewport.fireEvent('reloadMainMenu');
                viewport.down('SafeStartExtMain').on('mainMenuLoaded', function() {
                    Ext.History.setHash('');
                }, this, {
                    single: true
                });
            }
        });
    },

    logoutAction: function() {
        var viewport = this.getViewport();
        SafeStartExt.Ajax.request({
            url: 'user/logout',
            success: function() {
                delete SafeStartExt.companyRecord;
                viewport.fireEvent('reloadMainMenu');
                viewport.down('SafeStartExtMain').on('mainMenuLoaded', function() {
                    Ext.History.setHash('auth');
                }, this, {
                    single: true
                });
            }
        });
    },

    showProfileAction: function() {
        if (!this.profileDlg) {
            var viewport = this.getViewport();
            this.profileDlg = viewport.add(Ext.create('SafeStartExt.view.panel.UserProfile'));
            this.profileDlg.addListener('updateProfileAction', this.updateProfileAction, this);
        }
        this.profileDlg.show();
    },

    updateProfileAction: function(window, e) {
        var self = this;
        if (!this.userProfileModel) {
            this.userProfileModel = Ext.create('SafeStartExt.model.User');
        }
        // TODO: Validation
        //if (this.validateFormByModel(this.userProfileModel, this.getUpdateProfileForm())) {
        SafeStartExt.Ajax.request({
            url: 'user/' + SafeStartExt.getApplication().getUserRecord().get('id') + '/profile/update',
            data: this.getUpdateProfileForm().getValues(),
            success: function(result) {
                Ext.iterate(self.getUpdateProfileForm().getValues(), function(key, value) {
                    SafeStartExt.getApplication().getUserRecord().set(key, value);
                }, this);
                self.getTopNav().setUsername(SafeStartExt.getApplication().getUserRecord().get('firstName') + ' ' + SafeStartExt.getApplication().getUserRecord().get('lastName'));
                window.hide();
            }
        });
        //}
    }
});