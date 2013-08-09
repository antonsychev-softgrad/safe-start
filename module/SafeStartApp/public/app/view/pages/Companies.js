Ext.define('SafeStartApp.view.pages.Companies', {
    extend: 'Ext.Container',

    xtype: 'SafeStartCompaniesPage',

    config:{
        title: 'Companies',
        iconCls: 'team',

        styleHtmlContent: true,
        scrollable: true
    },

    initialize: function () {
        this.callParent();
        Ext.create('SafeStartApp.view.pages.toolbar.Main');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top',
            title: 'Companies'
        });
    }
});