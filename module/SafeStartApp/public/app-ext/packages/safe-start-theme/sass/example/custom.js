

Ext.theme.addManifest({
    xtype: 'widget.button',
    ui: 'tab',
    scale: 'large',
    config: {
        afterRender: function() {
            this.el.up('.widget-container').setStyle('background-color', '#383838');
        }
    }
});


Ext.theme.addManifest({
    xtype: 'widget.button',
    ui: 'green'
});

Ext.theme.addManifest({
    xtype: 'widget.button',
    ui: 'red'
});

Ext.theme.addManifest({
    xtype: 'widget.button',
    ui: 'blue'
});
