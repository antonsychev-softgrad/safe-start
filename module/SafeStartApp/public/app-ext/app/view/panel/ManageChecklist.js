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
                name: 'forms-panel',
                layout: 'card',
                items: [{
                    xtype: 'container',
                    name: 'blank'
                }, {
                    xtype: 'form',
                    name: 'root',
                    html: 'form for root type'
                }]
            }]
        });
        this.callParent();
    },

    switchForm: function (type) {
        this.getFormsPanel().getLayout().setActiveItem(this.getForm(type));
    },

    getForm: function (type) {
        this.getFormsPanel.down('component[name=' + type + ']');       
    },

    getFormsPanel: function () {
        return this.down('panel[name=forms-panel]');
    }
});