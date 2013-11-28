Ext.define('SafeStartExt.view.panel.ManageChecklist', {
    extend: 'Ext.panel.Panel',
    requires: [
        // 'SafeStartExt.view.form.UserProfile'
    ],
    xtype: 'SafeStartExtPanelManageChecklist',
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    

    initComponent: function() {
        Ext.apply(this, {
            items: [{
                xtype: 'panel',
                flex: 1,
                html: 'Tree'
            }, {
                xtype: 'panel',
                flex: 1,
                html: 'Form'
            }]
        });
        this.callParent();
    }
});