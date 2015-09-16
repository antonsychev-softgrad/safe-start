Ext.define('SafeStartExt.view.Carousel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.carousel',
    layout: {
        type: 'card'
    },

    initComponent: function() {
        Ext.apply(this, {
            bbar: ['->', {
                xtype: 'button',
                name: 'prev',
                text: '&laquo; Previous',
                disabled: true,
                handler: this.onPrev,
                scope: this
            }, {
                xtype: 'button',
                name: 'next',
                disabled: true,
                text: 'Next &raquo;',
                handler: this.onNext,
                scope: this
            }],
            listeners: {
                add: function () {
                    if (this.items.getCount() > 1) {
                        this.down('button[name=next]').enable();
                    }
                },
                scope: this
            }
        });

        this.callParent(arguments);
    },

    onPrev: function (btn) {
        var active = this.getLayout().prev();
        if (active) {
            if (this.items.first() === active) {
                btn.disable();
            }
            this.down('button[name=next]').enable();
        }
    },

    onNext: function (btn) {
        var active = this.getLayout().next();
        if (active) {
            if (this.items.last() === active) {
                btn.disable();
            }
            this.down('button[name=prev]').enable();
        }
    }
});

