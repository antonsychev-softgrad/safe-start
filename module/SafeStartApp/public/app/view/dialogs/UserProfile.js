Ext.define('SafeStartApp.view.dialogs.UserProfile', {

    extend: 'SafeStartApp.view.abstract.dialog',

    xtype: 'SafeStartUserProfileDialog',

    initialize: function () {
        this.callParent();
        this.add(
            {
                docked: 'top',
                xtype: 'toolbar',
                title: 'Update profile'
            }
        )
    }

});