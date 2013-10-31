Ext.define('SafeStartApp.view.pages.SystemStatistic', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.view.pages.panel.SystemGeneralReport',
        'SafeStartApp.view.pages.panel.InspectionBreakdownsReport',
        'SafeStartApp.view.pages.panel.CheckListsChangesReport'
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
                    xtype: 'SafeStartCheckListsChangesReportPanel'
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