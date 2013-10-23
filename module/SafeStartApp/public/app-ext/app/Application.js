Ext.ns('SafeStartExt');

SafeStartExt = SafeStartExt || {
    dateFormat: 'Y:m:d',
    timeFormat: 'H:i'
};

Ext.define('SafeStartExt.Application', {
    name: 'SafeStartExt',

    extend: 'Ext.app.Application',

    requires: [
        'SafeStartExt.view.Viewport',
        'SafeStartExt.model.User',
        'SafeStartExt.Ajax'
    ],

    controllers: [
        'Main',
        'Auth',
        'Companies',
        'Company',
        'Contact'
    ],
    userRecord: null,
    companyRecord: null,

    loadMainMenu: function () {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'web-panel/getMainMenu',
            success: function (result) {
                var mainView = me.getViewport().down('SafeStartExtMain');
                me.setUserData(result.userInfo);
                mainView.fireEvent('mainMenuLoaded', result.mainMenu || {});

                if (me.getUserRecord().get('role') === 'companyUser') {
                    mainView.fireEvent('changeCompanyAction', me.getUserRecord().getCompany());
                } else if (me.getUserRecord().get('role') !== 'guest') { //TODO: remove this section
                    me.getCompanyRecord().set('id', 1);
                    mainView.fireEvent('changeCompanyAction', me.getCompanyRecord());
                }
            }
        });
    },

    setUserData: function (data) {
        data = data || {};

        this.userRecord = SafeStartExt.model.User.create(data);
        if (data.company) {
            this.companyRecord = SafeStartExt.model.Company.create(data.company);
        } else {
            this.companyRecord = SafeStartExt.model.Company.create({});
        }
        this.userRecord.setCompany(this.companyRecord);
    },

    getUserRecord: function () {
        return this.userRecord;
    },

    getCompanyRecord: function () {
        return this.companyRecord;
    },

    launch: function () {
        this.viewport = SafeStartExt.view.Viewport.create({}); 
        this.viewport.on('reloadMainMenu', this.loadMainMenu, this);
        this.loadMainMenu();
    },

    getViewport: function () {
        return this.viewport;
    }
});
