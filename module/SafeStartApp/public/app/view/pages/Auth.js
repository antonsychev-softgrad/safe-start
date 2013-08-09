Ext.define('SafeStartApp.view.pages.Auth', {
    extend: 'Ext.Container',

    xtype: 'SafeStartAuthPage',

    config: {
        title: 'Welcome',
        iconCls: 'home',

        layout: 'fit',
        styleHtmlContent: false,
        scrollable: false
    },


    initialize: function () {
        this.callParent();
        this.AuthForm = this.add(Ext.create('SafeStartApp.view.forms.Auth'));
    }

});
