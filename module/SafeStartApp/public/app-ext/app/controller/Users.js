Ext.define('SafeStartExt.controller.Users', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'mainNavPanel'
    }, {
        selector: 'SafeStartExtComponentUsers SafeStartExtPanelUsersList',
        ref: 'usersListView'
    }, {
        selector: 'SafeStartExtComponentUsers SafeStartExtContainerTopNav',
        ref: 'usersTopNav'
    }, {
        selector: 'SafeStartExtComponentUsers panel[name=user-info]',
        ref: 'userInfoPanel'
    }, {
        selector: 'SafeStartExtComponentUsers SafeStartExtFormUser',
        ref: 'userFormPanel'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }],

    company: null,

    init: function () {
        this.control({
            'SafeStartExtPanelUsersList': {
                beforerender: this.refreshUsersList,
                changeUserAction: this.changeUserAction,
                addUserAction: this.addUserAction
            },
            'SafeStartExtMain': {
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtFormUser': {
                updateUserAction: this.updateUserAction,
                deleteUserAction: this.deleteUserAction,
                sendPasswordAction: this.sendPasswordAction,
            }
        });
    },

    addUserAction: function() {
        this._showEmptyForm();
        this._setFormButtonsVisible(false);
    },

    sendPasswordAction: function() {
        var userRecord = this.getUserFormPanel().getRecord();

        if (userRecord) {
            SafeStartExt.Ajax.request({
                url: 'user/' + userRecord.getId() + '/send-credentials',
                success: function (res) {
                    if (res.done) {
                    }
                }
            });
        }
    },

    deleteUserAction: function() {
        var form = this.getUserFormPanel(), 
            userModel = form.getRecord(),
            me = this;

        if (userModel) {
            SafeStartExt.Ajax.request({
                url: 'user/' + userModel.getId() + '/delete',
                success: function (res) {
                    if (res.done) {
                        me.refreshUsersList();
                        form.destroy();
                    }
                }
            });
        }
    },

    updateUserAction: function() {
        if (!this.getUserFormPanel().isValid()) return;

        var formValues = this.getUserFormPanel().getValues(),
            me = this;

        this.prepareValues(formValues);
        formValues.companyId = this.company.getId();

        SafeStartExt.Ajax.request({
            url: 'user/' + formValues.id + '/update',
            data: formValues,
            success: function (res) {
                if (res.done) {
                    me.refreshUsersList();
                    me.getUserFormPanel().destroy();
                }
            }
        });
    },

    changeCompanyAction: function(company) {
        this.company = company;

        if (this.getUserFormPanel()) {
            this.getUserFormPanel().destroy();
        }
        
        if (this.getUsersListView()) {
            this.refreshUsersList();
        }
    },

    changeUserAction: function(user) {
        this._showEmptyForm();
        this.getUserFormPanel().loadRecord(user);
    },

    _setFormButtonsVisible: function(visibility) {
        this.getUserFormPanel().down('button[name=delete-data]').setVisible(visibility);
        this.getUserFormPanel().down('button[name=send-password]').setVisible(visibility);
    },

    _showEmptyForm: function() {
        if (this.getUserFormPanel()) {
            this.getUserFormPanel().destroy();
        }

        var form = Ext.create('SafeStartExt.view.form.User');
        this.getUserInfoPanel().add(form);
    },

    prepareValues: function(values) {
        values.enabled = (values.enabled === 'on') ? 1 : 0;
    },

    refreshUsersList: function() {
        var store = this.getUsersListView().getListStore(),
            companyId = this.company.getId();

        store.getProxy().setExtraParam('companyId', companyId);
        store.load();
        
    }


});
