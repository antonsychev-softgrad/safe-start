Ext.define('SafeStartExt.view.panel.CompanyAdditionalInfo', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelCompanyAdditionalInfo',
    requires: [
        'SafeStartExt.view.form.CompanySettings'
    ],
    title: 'Info',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',
    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'container',
                flex: 1,
                padding: 20,
                cls: 'sfa-info-container',
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'SafeStartExtFormCompanySettings',
                    defaults: {
                        maxWidth: 400,
                        labelWidth: 130
                    }
                }]
            }]
        });
        this.callParent();
    }
});
