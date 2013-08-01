Ext.define('SafeStartApp.view.pages.Auth', {
    extend: 'Ext.Panel',

    requires: [
        'Ext.TitleBar'
    ],

    xtype: 'pageAuth',

    config:{
        title: 'Welcome',
        iconCls: 'home',

        styleHtmlContent: true,
        scrollable: true,

        items: {
            docked: 'top',
            xtype: 'titlebar',
            title: 'Welcome to Safe Start App'
        },

        html: [
            "content here"
        ].join("")
    }

});
