Ext.define('SafeStartApp.view.pages.panel.UpdateVehicleChecklist', {
    extend: 'SafeStartApp.view.components.UpdateChecklist',
    alias: 'widget.SafeStartUpdateVehicleChecklistPanel',

    _getDeleteUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/update';
    }

});

