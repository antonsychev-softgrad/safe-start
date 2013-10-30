Ext.define('SafeStartApp.view.pages.CompanySettings', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.CompanySettings',
        'SafeStartApp.view.forms.CompanySettings'
    ],

    xtype: 'SafeStartCompanySettingsPage',

    config: {
        title: 'Settings',
        iconCls: 'settings',
        styleHtmlContent: true,
        layout: 'card',
        tab: {
            action: 'company-settings'            
        },
        listeners: {
            scope: this,
            activate: function (page) {
                page.loadData();
            }
        }
    },

    initialize: function () {
        this.callParent();

        this.add({
            xtype: 'SafeStartCompanySettingsToolbar',
            docked: 'top'
        });

        this.add({
            xtype: 'panel',
            cls: 'sfa-info-container',
            layout: 'fit',
            items: [{
                xtype: 'SafeStartCompanySettingsForm'
            }]
        });
    },

    loadData: function () {
        var company = SafeStartApp.userModel.getCompany();
        if (company) {
            this.down('SafeStartCompanySettingsForm').setRecord(company);
        }
    }

});