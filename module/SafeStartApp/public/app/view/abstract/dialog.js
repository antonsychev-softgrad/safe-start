Ext.define('SafeStartApp.view.abstract.dialog', {

    extend: 'Ext.Panel',

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
        height: Ext.filterPlatform('ie10') ? '30%' : Ext.os.deviceType == 'Phone' ? 220 : 400,
        styleHtmlContent: true,
        scrollable: false,
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
                        ui: 'confirm'
                    }
                ]
            }
        ]
    }

});