Ext.define('SafeStartApp.controller.VehicleChecklist', {
    extend: 'SafeStartApp.controller.DefaultChecklist',

    config: {
        control: {
            navMain: {
                leafitemtap: 'onSelectAction',
                selectionchange: 'onSelectChangeAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage] > panel[name=checklist] > nestedlist[name=checklist-tree]',
            infoPanel: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage] > panel[name=checklist] > panel[name=field-info]',
            addButton: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage] > panel[name=checklist] > nestedlist[name=checklist-tree] > toolbar > button[action=add-field]'
        }
    },

    _getDeleteUrl: function() {
        return 'checklist/' + this.currentForm.getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'checklist/' + this.currentForm.getValues().id + '/update';
    }

});