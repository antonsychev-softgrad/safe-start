Ext.define('SafeStartApp.view.dialogs.UserProfile', {

    extend: 'SafeStartApp.view.abstract.dialog',

    xtype: 'SafeStartUserProfileDialog',

    initialize: function () {
        this.callParent();
        this.profileForm = this.add(Ext.create('SafeStartApp.view.forms.UserProfile'));
        this.profileForm.setRecord(SafeStartApp.userModel);
    }

});