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
        'Contact',
        'Company'
    ],
    userRecord: null,

    loadMainMenu: function () {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'web-panel/getMainMenu',
            success: function (result) {
                me.setUserData(result.userInfo);
                me.getViewport().fireEvent('mainMenuLoaded', result.mainMenu || {});
            }
        });
    },

    setUserData: function (data) {
        data = data || {};
        if (this.userRecord) {
            this.userRecord.destroy();
        }
        this.userRecord = SafeStartExt.model.User.create(data);
    },

    getUserRecord: function () {
        return this.userRecord;
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
