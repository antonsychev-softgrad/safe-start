Ext.define('SafeStartApp.view.pages.Auth', {
    extend: 'Ext.Container',

    xtype: 'SafeStartAuthPage',

    config: {
        title: 'Welcome',
        iconCls: 'home',

        layout: 'fit',
        styleHtmlContent: false,
        scrollable: false,

        html: [
            '<div class="logo"><img height=100 width="381" src="/resources/img/logo-small.png" /><div>'
        ].join("")
        
    },


    initialize: function () {
        this.callParent();
        this.AuthForm = this.add(Ext.create('SafeStartApp.view.forms.Auth'));
    }

});
