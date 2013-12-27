Ext.define('SafeStartExt.view.panel.ManageDefaultChecklist', {
    extend: 'SafeStartExt.view.abstract.ManageChecklist',

    xtype: 'SafeStartExtPanelManageDefaultChecklist',

    requires: [
        'SafeStartExt.store.DefaultChecklistTree'
    ],
    title: 'Default Checklist',


    initTreeStore: function () {
        this.treeStore = SafeStartExt.store.DefaultChecklistTree.create({
            listeners: {
                beforeremove: this.onBeforeRemove,
                scope: this
            }
        });
    },

    _getDeleteUrl: function() {
        return 'admin/checklist/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'admin/checklist/' + this.getForm().getValues().id + '/update';
    }
});
