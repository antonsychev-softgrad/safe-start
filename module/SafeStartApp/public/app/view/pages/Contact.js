Ext.define('SafeStartApp.view.pages.Contact', {
    extend: 'Ext.Panel',

    requires: [
        'Ext.TitleBar'
    ],

    xtype: 'pageContact',

    config:{
        title: 'Contact',
        iconCls: 'user',

        styleHtmlContent: true,
        scrollable: true,

        items: [
            {
                docked: 'top',
                xtype: 'titlebar',
                title: 'Contact Us'
            }
        ],

        html: [
            "content here"
        ].join("")
    }

});