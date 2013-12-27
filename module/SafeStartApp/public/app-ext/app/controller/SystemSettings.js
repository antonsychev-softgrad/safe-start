Ext.define('SafeStartExt.controller.SystemSettings', {
    extend: 'Ext.app.Controller',

    init: function () {
        this.control({
            'SafeStartExtPanelManageDefaultChecklist': {
                saveField: this.saveChecklistField,
                deleteField: this.deleteChecklistField
            }
        });
    },

    saveChecklistField: function (form) {
        var record = form.getRecord();
        var data = record.getWriteData();
        SafeStartExt.Ajax.request({
            url: 'admin/checklist/' + record.get('id') + '/update',
            data: record.getWriteData(),
            success: function (result) {
                record.beginEdit();
                if (! record.get('id')) {
                    record.set('id', result.fieldId);
                }
                record.modified = {};
                record.endEdit();
                form.loadRecord(record);
            }
        });
    },

    deleteChecklistField: function (form) {
        var record = form.getRecord();
        var parent = record.parentNode;
        if (record.get('id') === 0 && parent) {
            parent.removeChild(record);
            if (parent.getDepth()) {
                form.up('SafeStartExtPanelManageDefaultChecklist').down('treepanel').getSelectionModel().select(parent);
            }
            return;
        }

        Ext.Msg.confirm({
            msg: 'Do you sure you want to delete this field from checklist?',
            buttons: Ext.Msg.YESNO,
            fn: function (result) {
                if (result !== 'yes') {
                    return;
                }
                SafeStartExt.Ajax.request({
                    url: 'admin/checklist/' + record.get('id') + '/delete',
                    success: function(result) {
                        parent.removeChild(record);
                        if (parent && parent.getDepth() != 0) {
                            form.up('SafeStartExtPanelManageDefaultChecklist').down('treepanel').getSelectionModel().select(parent);
                        }
                    }
                });
            }
        });
    }
});
