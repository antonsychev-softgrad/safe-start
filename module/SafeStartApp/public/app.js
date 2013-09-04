//<debug>
Ext.Loader.setPath({
    'Ext': 'touch/src',
    'Ext.ux': 'app/ux'
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
    userModel: {},
    companyModel: {},

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

    loadMainMenu: function() {
        this.AJAX('web-panel/getMainMenu', {}, function(result) {
            SafeStartApp.userModel.setData(result.userInfo || {});
            SafeStartApp.setViewPort(result.mainMenu || null);
        });
    },

    setViewPort: function(menu) {
        var viewPort = Ext.Viewport.down('SafeStartViewPort');
        if (viewPort) {
            Ext.each(viewPort.getInnerItems(), function (item) {
                if (item.onShow) {
                    item.removeListener('show', item.onShow);
                }
            });
        }

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

        Ext.viewport.Default.prototype.doBlurInput = function(e) {
            var target = e.target,
                focusedElement = this.focusedElement;
            //In IE9/10 browser window loses focus and becomes inactive if focused element is <body>. So we shouldn't call blur for <body>
            if (focusedElement && focusedElement.nodeName.toUpperCase() != 'BODY' && !this.isInputRegex.test(target.tagName)) {
                delete this.focusedElement;
                if (typeof focusedElement == 'object' && typeof focusedElement.blur == 'function')  focusedElement.blur();
            }
        };
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
        'pages.Contact',
        'pages.Companies',
        'pages.Company',
        'pages.Users',
        'pages.SystemSettings'
    ],

    controllers: [
        'Main',
        'Auth',
        'Companies',
        'Users',
        'Company',
        'DefaultVehicles',
        'CompanyVehicles',
        'UserVehicles'
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

    eventPublishers: {
        touchGesture: {
            recognizers: {
                mousewheeldrag: {
                    xclass: 'Ext.ux.event.recognizer.MouseWheelDrag'
                }
            }
        }
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
