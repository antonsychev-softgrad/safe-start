Ext.define('SafeStartExt.view.panel.InspectionInfo', {
    extend: 'Ext.panel.Panel',
    requires: [
    ],
    xtype: 'SafeStartExtPanelInspectionInfo',
    cls:'sfa-previous-inspections-info',
    ui: 'light',
    hidden: true,
    html: 'Inspection details',

    initComponent: function () {
        this.callParent();
    },

    setInspectionInfo: function (inspection, data) {
        this.show();
    }

});
