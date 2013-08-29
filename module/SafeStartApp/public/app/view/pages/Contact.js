Ext.define('SafeStartApp.view.pages.Contact', {

    extend: 'Ext.Container',

    xtype: 'SafeStartContactPage',
    requires: [
        'SafeStartApp.view.forms.Contact'
    ],

    config:{
        title: 'Contact',
        iconCls: 'info',
        styleHtmlContent: true,
        scrollable: true
    },

    initialize: function () {
        this.callParent();
        this.ContactForm = this.add(Ext.create('SafeStartApp.view.forms.Contact'));
    }

});