Ext.define('SafeStartExt.view.CarouselTb', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.carouseltb',
    directionals: true ,
    initComponent: function() {
        var me = this;

        me.items = [{
            xtype: 'tbfill'
        }, {
            xtype: 'tbfill'
        }];

        me.callParent(arguments);
    },
    handleCarouselEvents: function(carousel) {
        var me = this;
        me.relayEvents(carousel, ['carouselchange']);
        me.on('carouselchange', me.onCarouselChange, me, {
            buffer: 20
        });
    },
    onCarouselChange: function(carousel, item) {
        var me = this;
        console.log(me);
        var navSprites = me.down('draw').surface.getGroup('carousel');
        navSprites.setAttributes({
            opacity: 0.2
        }, true);
        var i = carousel.items.indexOf(item);
        navSprites.each(function(s) {
            if (s.index == i) {
                s.animate({
                    to: {
                        opacity: 0.7
                    }
                });
            }
        });
    },
    onRender: function() {
        var me = this;

        var prev = {
            text: '<',
            handler: function() {
                me.ownerCt.down('carousel').previousChild();
            }
        };

        var next = {
            text: '>',
            handler: function() {
                me.ownerCt.down('carousel').nextChild();
            }
        };

        Ext.suspendLayouts();
        if (me.directionals) {
            me.insert(0, prev);
            me.insert(me.items.items.length, next);
        }

        var index = me.items.indexOf(me.down('tbfill'));
        var circles = [];
        var x = 0;
        var i = 0;
        Ext.each(me.ownerCt.down('carousel').items.items, function(item) {
            var config = {
                type: 'circle',
                x: x,
                y: 0,
                index: i,
                radius: 1,
                fill: 'black',
                opacity: i == 0 ? 0.7 : 0.2,
                group: 'carousel'
            };
            circles.push(config);
            x += 3;
            i++;
        });
        me.insert(index + 1, {
            xtype: 'draw',
            height: 12,
            items: circles
        });

        Ext.resumeLayouts();

        Ext.defer(function() {
            var c = me.down('draw').surface.getGroup('carousel');
            c.each(function(s) {
                s.on({
                    click: function(s) {
                        c.setAttributes({
                            opacity: 0.2
                        }, true);
                        var carousel = me.ownerCt.down('carousel');
                        carousel.showChild(carousel.items.items[s.index]);
                    }
                });
            });
        }, 2);

        var carousel = me.ownerCt.down('carousel');
        if (carousel) {
            me.handleCarouselEvents(carousel);
        }

        me.callParent(arguments);
    }
});