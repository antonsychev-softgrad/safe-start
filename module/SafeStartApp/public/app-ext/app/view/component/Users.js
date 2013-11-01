Ext.define('SafeStartExt.view.component.Users', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.UsersList',
        'SafeStartExt.view.form.User'
    ],
    xtype: 'SafeStartExtComponentUsers',
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
                titleText: 'Users'
            }, {
                xtype: 'container',
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                flex: 1,
                items: [{
                    xtype: 'SafeStartExtPanelUsersList',
                    flex: 1,
                    maxWidth: 250
                }, {
                    cls: 'sfa-info-container',
                    xtype: 'panel',
                    type: 'vbox',
                    flex: 2,
                    padding: 20,
                    ui: 'transparent',
                    name: 'company-info'
                }]
            }]
        });
        this.callParent();
    }
});
