/*Ext.define('SafeStartApp.view.dialogs.UserProfile', {

    extend: 'SafeStartApp.view.base.dialog',

    xtype: 'SafeStartUserProfileDialog',

    initialize: function () {
        this.callParent();
        this.profileForm = this.add(Ext.create('SafeStartApp.view.forms.UserProfile'));
        this.profileForm.setRecord(SafeStartApp.userModel);
    }

});*/

Ext.define('SafeStartApp.view.dialogs.UserProfile', {
    extend: 'Ext.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartUserProfileDialog',
    config:{
        modal: true,
        hideOnMaskTap: true,
        showAnimation: {
            type: 'popIn',
            duration: 250,
            easing: 'ease-out'
        },
        hideAnimation: {
            type: 'popOut',
            duration: 250,
            easing: 'ease-out'
        },
        centered: true,
        width: Ext.filterPlatform('ie10') ? '100%' : (Ext.os.deviceType == 'Phone') ? 460 : 600,
        height: Ext.filterPlatform('ie10') ? '30%' : Ext.os.deviceType == 'Phone' ? 370 : 500,
        styleHtmlContent: true,
        scrollable: false,
        cls: 'sfa-modal-form',
        items: [
            {
                xtype: 'toolbar',
                docked: 'bottom',
                items: [
                    {
                        text: 'Cancel',
                        ui: 'action',
                        handler: function() {
                            this.up('SafeStartUserProfileDialog').hide();
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        text: 'Save',
                        action: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartUserProfileDialog').fireEvent('save-data', this.up('SafeStartUserProfileDialog'));
                        }
                    }
                ]
            }
        ]
    },

    initialize: function () {
        this.callParent();
        this.profileForm = this.add(Ext.create('SafeStartApp.view.forms.UserProfile'));
        this.profileForm.setRecord(SafeStartApp.userModel);
    }

});