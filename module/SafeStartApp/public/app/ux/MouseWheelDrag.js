Ext.define('Ext.ux.event.recognizer.MouseWheelDrag', {
    extend: 'Ext.event.recognizer.Touch',
 
    requires: ['Ext.event.Dom'],
 
    handledEvents: ['dragstart', 'drag', 'dragend'],
 
    constructor: function() {
        this.callParent(arguments);
 
        this.onMouseWheel = Ext.Function.bind(this.onMouseWheel, this);
        this.fireDragEnd = Ext.Function.bind(this.fireDragEnd, this);
 
        document.addEventListener('mousewheel', this.onMouseWheel, true);
    },
 
    onMouseWheel: function(e) {
        var helper = Ext.event.publisher.Dom.prototype,
            target = helper.getElementTarget(e.target),
            targets = helper.getBubblingTargets(target),
            deltaX = e.hasOwnProperty('wheelDeltaX') ? e.wheelDeltaX : e.wheelDelta,
            deltaY = e.hasOwnProperty('wheelDeltaY') ? e.wheelDeltaY : e.wheelDelta,
            touches = [{
                targets: targets
            }],
            lastPoint, time;
 
        e = new Ext.event.Dom(e);
        time = e.time;
 
        this.lastEvent = e;
        this.lastTouches = touches;
 
        if (!this.startPoint) {
            this.startTime = time;
            this.startPoint = lastPoint = {
                x: e.pageX,
                y: e.pageY
            };
 
            this.previousPoint = lastPoint;
            this.previousTime = time;
 
            this.lastPoint = lastPoint;
            this.lastTime = time;
 
            this.fire('dragstart', e, touches, this.getInfo(e));
        }
 
        lastPoint = this.lastPoint;
 
        this.previousTime = this.lastTime;
        this.previousPoint = lastPoint;
 
        this.lastPoint = {
            x: lastPoint.x + deltaX,
            y: lastPoint.y + deltaY
        };
        this.lastTime = time;
 
        this.fire('drag', e, touches, this.getInfo(e));
 
        clearTimeout(this.dragEndTimer);
        this.dragEndTimer = setTimeout(this.fireDragEnd, 350);
    },
 
    fireDragEnd: function() {
        var e = this.lastEvent;
 
        this.fire('dragend', e, this.lastTouches, this.getInfo(e));
 
        this.startTime = 0;
        this.previousTime = 0;
        this.lastTime = 0;
 
        this.startPoint = null;
        this.previousPoint = null;
        this.lastPoint = null;
        this.lastMoveEvent = null;
        this.lastEvent = null;
        this.lastTouches = null;
    },
 
    getInfo: function(e, touch) {
        return Ext.event.recognizer.Drag.prototype.getInfo.apply(this, arguments);
    }
});