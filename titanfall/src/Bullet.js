var Bullet = cc.Sprite.extend({
    ctor:function( x, y , bulletDirection , tag )
    {
        this._super();
        this.initWithFile('images/Bullet.png');

        this.tag = tag;
        this.x = x;
        this.y = y;
        this._vx = 5;
        this.bulletDirection = bulletDirection;
        
        
        this.setBulletDirection();
        this.updateSpritePosition();
    },

    getTag : function ()
    {
        return this.tag;
    },

    getX : function ()
    {
        return this.x;
    },

    update : function ( dt )
    {
        this.updateMovement();
        this.updateSpritePosition();
    },

    updateMovement : function ()
    {
    	if( this.bulletDirection == "left")
    	{
    		this.x -= this._vx;
    	}
    	if( this.bulletDirection == "right")
    	{
    		this.x += this._vx;
    	}
    },

    updateSpritePosition : function ()
    {
    	this.setPosition( cc.p( Math.round( this.x ),
    							Math.round( this.y ) ) );
    },

    setBulletDirection : function ()
    {
        if( this.bulletDirection == "left")
            this.setFlippedX(true);
        else
            this.setFlippedX(false);
    },

    handleCollision : function ( rect )
    {
        if( cc.rectIntersectsRect( rect, this.getRect() ) )
            return true;
        return false;
    },

    getRect : function ()
    {
        return this.getBoundingBox();
    },

    checkBoundary : function ()
    {
        if( this.x > g_sharedGameLayer.getMaxX()  && this.x > 0 )
            return true;
        if( this.y > g_sharedGameLayer.getMaxY()  && this.y > 0)
            return true;
        return false;
    },

    
});