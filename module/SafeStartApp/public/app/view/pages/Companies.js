Ext.define('SafeStartApp.view.pages.Companies', {

    extend: 'Ext.Container',

    xtype: 'SafeStartCompaniesPage',

    config:{
        title: 'Companies',
        iconCls: 'user',

        styleHtmlContent: true,
        scrollable: true
    },

    initialize: function () {
        this.callParent();

    }

});