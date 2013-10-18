Ext.define('SafeStartApp.view.pages.SystemStatistic', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.view.pages.panel.SystemGeneralReport',
        'SafeStartApp.view.pages.panel.InspectionBreakdownsReport'
    ],

    xtype: 'SafeStartSystemStatisticPage',

    config: {
        title: 'Statistic',
        iconCls: 'info',
        styleHtmlContent: true,
        layout: 'card',
        tab: {
            action: 'system-statistic'
        },
        items: [

        ],

        listeners: {
            scope: this,
            show: function (page) {
                page.down('tabpanel').getActiveItem().loadData();
            }
        }
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Main');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top'
        });

        this.add(this.getTabPanel());

    },

    getTabPanel: function () {
        return {
            cls: 'sfa-info-container sfa-system-settings',
            xtype: 'tabpanel',
            defaults: {
                styleHtmlContent: true
            },
            items: [
                {
                    xtype: 'SafeStartSystemGeneralReportPanel'
                },
                {
                    xtype: 'SafeStartInspectionBreakdownsReportPanel'
                },
                {
                    xtype: 'panel',
                    title: 'CheckLists Changes',
                    name: 'changes',
                    html: "CheckLists Changes",
                    minHeight: 300,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    }
                }
            ],
            listeners: {
                'activeitemchange': function(tabs, newPanel, oldPanel) {
                    if (newPanel && newPanel.loadData) newPanel.loadData();
                }
            }
        };
    }
});