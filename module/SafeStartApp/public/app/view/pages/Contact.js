Ext.define('SafeStartApp.view.pages.Contact', {

    extend: 'Ext.Container',

    xtype: 'SafeStartContactPage',

    config:{
        title: 'Contact',
        iconCls: 'user',

        styleHtmlContent: true,
        scrollable: true
    },

    initialize: function () {
        this.callParent();
        this.ContactForm = this.add(Ext.create('SafeStartApp.view.forms.Contact'));
    }

});