var Background = cc.Sprite.extend(
{
    ctor:function(x,y)
    {
        this._super();
        this.initWithFile('images/background2.png');

        this.x = x;
        this.y = y;
        this.setAnchorPoint(cc.p(0,0));
        this.setPosition(new cc.Point(x,y));
    },
    update : function( dt)
    {
    	this.updatePostion();
    },
    updatePostion:function()
    {
    this.setPosition( cc.p( this.x, this.y ) );
    } 

    
});