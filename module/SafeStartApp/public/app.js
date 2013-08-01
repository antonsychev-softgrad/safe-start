//<debug>
Ext.Loader.setPath({
    'Ext': 'touch/src'
});
//</debug>

SafeStartApp = {

    version: "1.0",

    baseHref: "http://safe-start.dev/api/",

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
                var text = response.responseText;
            }
        });
    },

    getHash: function(length) {
        length = length || 12;
        return Math.random().toString(36).substring(length);
    }

};

Ext.application({
    name: 'SafeStartApp',

    requires: [
        'Ext.MessageBox'
    ],

    views: [
        'Main'
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

        SafeStartApp.AJAX('index/ping');

        // Destroy the #appLoadingIndicator element
        Ext.fly('appLoadingIndicator').destroy();

        // Initialize the main view
        Ext.Viewport.add(Ext.create('SafeStartApp.view.Main'));
    },

    setViewPort: function() {

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
