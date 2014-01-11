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
        afterrender: function (page) {

        }
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'Statistic'
            }, {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'tabpanel',
                    cls: 'sfa-info-container sfa-system-settings sfa-vehicles-tabpanel',
                    height: '100%',
                    items: [{
                        xtype: 'SafeStartExtPanelSystemGeneralReport'
                    }, {
                        xtype: 'SafeStartExtPanelInspectionBreakdownsReport'
                    }, {
                        xtype: 'SafeStartExtPanelCheckListsChangesReport'
                    }]
                }]
            }]
        });
        this.callParent();
    }
});