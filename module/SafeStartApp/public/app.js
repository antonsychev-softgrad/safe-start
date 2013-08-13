//<debug>
Ext.Loader.setPath({
    'Ext': 'touch/src'
});
//</debug>
SafeStartApp = SafeStartApp || {
    version: "1.0",

    baseHref: "http://safe-start.dev/api/",

    defMenu: [
        'Auth',
        'Contact'
    ]
};

Ext.apply(SafeStartApp,  {
    AJAX: function(url, data, successCalBack, failureCalBack) {
        var self = this;
        var meta = {
            requestId: 'WEB_'+this.getHash(12)
        };
        data = data || {test: 1};
        Ext.Viewport.setMasked({ xtype: 'loadmask' });
        Ext.Ajax.request({
            url: this.baseHref + url,
            params: Ext.encode({
                meta: meta,
                data: data
            }),
            success: function(response){
                Ext.Viewport.setMasked(false);
                var result = Ext.decode(response.responseText);
                if (result.meta && result.meta.status == 200 && !parseInt(result.meta.errorCode)) {
                    if (successCalBack && typeof successCalBack == 'function') successCalBack(result.data || {});
                } else {
                    self.showRequestFailureInfoMsg(result, failureCalBack);
                }
            },
            failure : function(response){
                Ext.Viewport.setMasked(false);
                self.showRequestFailureInfoMsg(Ext.decode(response.responseText), failureCalBack);
            }
        });
    },

    getHash: function(len, charSet) {
        len = len || 12;
        charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var randomString = '';
        for (var i = 0; i < len; i++) {
            var randomPoz = Math.floor(Math.random() * charSet.length);
            randomString += charSet.substring(randomPoz,randomPoz+1);
        }
        return randomString;
    },

    showRequestFailureInfoMsg: function(result, failureCalBack) {
        var func = Ext.emptyFn();
        if (failureCalBack && typeof failureCalBack == 'function') func = failureCalBack;
        var errorMessage = '';
        if (result.data && result.data.errorMessage) errorMessage = result.data.errorMessage;
        this.showFailureInfoMsg(errorMessage, func);
    },

    showFailureInfoMsg: function(msg, failureCalBack) {
        msg = msg || 'Operation filed';
        Ext.Msg.alert("Server response error", msg, failureCalBack);
    },

    showInfoMsg: function(msg) {
        msg = msg || 'Info message'
        Ext.Msg.alert("Info", msg, Ext.emptyFn());
    },

    userModel: {},

    loadMainMenu: function() {
        this.AJAX('web-panel/getMainMenu', {}, function(result) {
            SafeStartApp.setViewPort(result.mainMenu || null);
            SafeStartApp.userModel.setData(result.userInfo || {});
        });
    },

    setViewPort: function(menu) {

        Ext.Viewport.removeAll(true);

        this.currentMenu = [];

        Ext.each(menu || this.defMenu, function(item) {
            SafeStartApp.currentMenu.push(
                {
                    xclass: 'SafeStartApp.view.pages.'+item
                }
            )
        }, this);

        Ext.define('SafeStartApp.view.ViewPort', {
            extend: 'SafeStartApp.view.Main',
            xtype: 'SafeStartViewPort',
            config: {
                tabBarPosition: 'bottom',
                items: SafeStartApp.currentMenu
            }
        });

        Ext.Viewport.add({ xtype: 'SafeStartViewPort' });
    }
});

Ext.application({
    name: 'SafeStartApp',

    requires: [
        'Ext.MessageBox'
    ],

    views: [
        'Main',
        'pages.Auth',
        'pages.Contact'
    ],

    controllers: [
        'Main',
        'Auth'
    ],

    icon: {
        '57': 'resources/icons/Icon.png',
        '72': 'resources/icons/Icon~ipad.png',
        '114': 'resources/icons/Icon@2x.png',
        '144': 'resources/icons/Icon~ipad@2x.png'
    },

    isIconPrecomposed: true,

    startupImage: {
        '320x460': 'resources/startup/320x460.jpg',
        '640x920': 'resources/startup/640x920.png',
        '768x1004': 'resources/startup/768x1004.png',
        '748x1024': 'resources/startup/748x1024.png',
        '1536x2008': 'resources/startup/1536x2008.png',
        '1496x2048': 'resources/startup/1496x2048.png'
    },

    launch: function() {
        var self = this;

        // Destroy the #appLoadingIndicator element
        Ext.fly('appLoadingIndicator').destroy();

        // Load current user menu and update view port
        SafeStartApp.userModel = Ext.create('SafeStartApp.model.User');
        SafeStartApp.loadMainMenu();
    },

    onUpdated: function() {
        Ext.Msg.confirm(
            "Application Update",
            "This application has just successfully been updated to the latest version. Reload now?",
            function(buttonId) {
                if (buttonId === 'yes') {
                    window.location.reload();
                }
            }
        );
    }
});
