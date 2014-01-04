Ext.define('SafeStartExt.view.component.CompanySettings', {
    extend: 'Ext.panel.Panel',

    xtype: 'SafeStartExtComponentCompanySettings',

    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.form.CompanySettings'
    ],
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'Company Settings'
            }, {
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