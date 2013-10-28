/*
 * This file is generated as a starting point by Sencha Cmd - it will not be replaced or
 * updated by "sencha package upgrade".
 * 
 * This file can be removed and the script tag in theme.html removed if this theme does
 * not need custom additional manifest or shortcut entries. These are documented in
 * ./packages/ext-theme-base/sass/example/render.js.
 */

//Ext.theme.addManifest();

//Ext.theme.addShortcuts();
    // Ext.theme.addManifest({
    //     xtype: 'widget.panel',
    //     cls: 'sfa-bottomnav',
    //     config: {
    //         items: [{
    //             xtype: 'button',
    //             ui: 'tab',
    //             scale: 'large'
    //         }]
    //     }
        //     floating: false,
        //     width: 200,
        //     items: [{
        //         text: 'test',
        //         cls: 'x-menu-item-active'
        //     }]
        // }
    // });

    Ext.theme.addManifest({
        xtype: 'widget.button',
        ui: 'tab',
        scale: 'large',
        config: {
            afterRender: function() {
                var me = this,
                    el = me.el;

                // el.addCls(Ext.baseCSSPrefix + 'column-header-align-' + me.align).addClsOnOver(me.overCls);
                // el.up('.widget-container').setStyle({
                // });
                el.up('.widget-container').addCls('sfa-bottomnav');
                el.setStyle('margin-top', '12px');
                el.setStyle('margin-bottom', '3px');

            }
        }
    });

    Ext.theme.addManifest({
        xtype: 'widget.button',
        ui: 'green',
        scale: 'medium'
        // delegate: '.x-menu-item-link',
        // filename: 'menu-item-active',
        // config: {
        //     floating: false,
        //     width: 200,
        //     items: [{
        //         text: 'test',
        //         cls: 'x-menu-item-active'
        //     }]
        // }
    });