Ext.define('SafeStartApp.view.pages.panel.UpdateChecklist', {
    extend: 'SafeStartApp.view.components.UpdateChecklist',
    alias: 'widget.SafeStartUpdateChecklistPanel',

    _getDeleteUrl: function() {
        return 'admin/checklist/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'admin/checklist/' + this.getForm().getValues().id + '/update';
    }
});

