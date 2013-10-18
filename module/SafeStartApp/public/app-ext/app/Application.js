Ext.define('SafeStartExt.Application', {
    name: 'SafeStartExt',

    extend: 'Ext.app.Application',

    requires: [
        'SafeStartExt.view.Viewport',
        'SafeStartExt.model.User'
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
        Ext.Ajax.request({
            url: '/api/web-panel/getMainMenu',
            method: 'GET',
            success: function (res) {
                var result = Ext.decode(res.responseText),
                    data = result.data || {};
                me.setUserData(data.userInfo);
                me.viewport.fireEvent('mainMenuLoaded', data.mainMenu || []);
            },
            failure: function () {

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
    }
});
