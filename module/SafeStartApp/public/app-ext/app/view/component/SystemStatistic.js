Ext.define('SafeStartExt.view.component.SystemStatistic', {
    extend: 'Ext.panel.Panel',

    xtype: 'SafeStartExtComponentSystemStatistic',

    requires: [
        'SafeStartExt.view.panel.SystemGeneralReport',
        'SafeStartExt.view.panel.InspectionBreakdownsReport',
        'SafeStartExt.view.panel.CheckListsChangesReport'
    ],

    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',

    listeners: {
        scope: this,
        show: function (page) {
            // var active = page.down('tapbanel').getActiveItem();
            // if (active && active.loadData) {
            //     active.loadData();
            // }
        }
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'Statistic'
            }, {
                xtype: 'container',
                //name: '-container',
                items: [this.getTabPanel()]
            }]
        });
        this.callParent();
    },

    getTabPanel: function () {
        return {
            cls: 'sfa-info-container sfa-system-settings',
            xtype: 'tabpanel',
            height: '100%',
            items: [{
                xtype: 'SafeStartExtPanelSystemGeneralReport'
            }, {
                xtype: 'SafeStartExtPanelInspectionBreakdownsReport'
            }, {
                xtype: 'SafeStartExtPanelCheckListsChangesReport'
            }],
            listeners: {
                activeitemchange: function(tabs, newPanel, oldPanel) {
                    if (newPanel && newPanel.loadData) {
                        newPanel.loadData();
                    }
                }
            }
        };
    }
});