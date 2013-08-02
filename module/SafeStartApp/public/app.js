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
        var meta = {
            requestId: this.getHash()
        };
        data = data || {};
        Ext.Ajax.request({
            url: this.baseHref + url,
            params: {
                meta: meta,
                data: data
            },
            success: function(response){
                var result = Ext.decode(response.responseText);
                if (result.meta && result.meta.status == 200) {
                    if (successCalBack && typeof successCalBack == 'function') successCalBack(result.data || {});
                }

            }
        });
    },

    getHash: function(length) {
        length = length || 12;
        return Math.random().toString(36).substring(length);
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

        Ext.Viewport.setMasked({ xtype: 'loadmask' });

        SafeStartApp.AJAX('web-panel/index', {}, function(result) {
            self.setViewPort(result.mainMenu || null);
        });

    },

    setViewPort: function(menu) {
        SafeStartApp.currentMenu = [];

        Ext.each(menu || SafeStartApp.defMenu, function(item) {
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


        Ext.Viewport.setMasked(false);

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
