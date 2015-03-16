Ext.define('SafeStartApp.view.base.dialog', {
    extend: 'Ext.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartAbstractDialog',

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
        width: Ext.filterPlatform('ie10') ? '100%' : (Ext.os.deviceType == 'Phone') ? 260 : 400,
        height: Ext.filterPlatform('ie10') ? '30%' : Ext.os.deviceType == 'Phone' ? 220 : 350,
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
                            this.up('SafeStartAbstractDialog').hide();
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        text: 'Save',
                        action: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartAbstractDialog').fireEvent('save-data', this.up('SafeStartAbstractDialog'));
                        }
                    }
                ]
            }
        ]
    }

});