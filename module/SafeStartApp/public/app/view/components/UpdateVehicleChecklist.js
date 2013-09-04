Ext.define('SafeStartApp.view.components.UpdateVehicleChecklist', {
    extend: 'SafeStartApp.view.components.UpdateChecklist',

    _getDeleteUrl: function() {
        return 'checklist/' + this.currentForm.getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'checklist/' + this.currentForm.getValues().id + '/update';
    }

});