var Enemy = cc.Sprite.extend({
    ctor: function( x, y ) 
    {
        this._super();
        this.initWithFile( 'images/enemy.png' );


        this.x = x;
        this.y = y;
        this.g = -1;
        this.vx = 0;
        this.vy = 0;


        this.flippedX = false;

        this.ground = null;
        this.blocks = [];
        this.bullets = [];
        this.bulletCount = 0;


        cc.Director.getInstance().getScheduler().scheduleCallbackForTarget(this, this.spriteUpdate , 1  , cc.REPEAT_FOREVER  ,  0 , !this._isRunning );
        this.updateSpritePosition();
    },

    update : function ()
    {
        this.handleCollision();
    },

    updateSpritePosition: function() 
    {
        this.setPosition( cc.p( Math.round( this.x ),
                                Math.round( this.y ) ) );
    },
    
    spriteUpdate: function( ) 
    {

        var currentPositionRect = this.getEnemyRect();

        //this.updateYMovement();
        
        this.updateXMovement();
        this.updateSpritePosition();


        var newPositionRect = this.getEnemyRect();
        this.handleCollision( currentPositionRect,
                              newPositionRect );
        this.handleCollision();
        
    },

    move : function ( direction )
    {
        if ( direction == "right") 
        {
            this.x += 20 ;
            this.flippedX = true;
            this.setFlippedX(this.flippedX);
        }
        else
        {
            this.x -= 20;
            this.flippedX = false;
            this.setFlippedX(this.flippedX);
        }
    },

    updateXMovement: function() 
    {
            if ( this.closeTo( g_player , 200) ) 
            {

                this.attack();
                
            } 
            else
            {
                var random = Math.floor(( Math.random() *1 )+1);
                if( random == 1)
                {
                    this.move("left")
                }
                else
                {
                    this.move("right");
                }
            }


        
        
    },

    updateYMovement: function() 
    {
        /*if ( this.ground ) 
        {
            this.vy = 0;
            if ( this.jump ) 
            {
                this.vy = this.jumpV;
                this.y = this.ground.getTopY() + this.vy;
                this.ground = null;
            }
        } 
        else 
        {
            this.vy += this.g;
            this.y += this.vy;
        }*/
    },

    isSameDirection: function( dir )
    {
        return ( ( ( this.vx >=0 ) && ( dir >= 0 ) ) ||
                 ( ( this.vx <= 0 ) && ( dir <= 0 ) ) );
    },

    intializeBullet : function()
    {
        if( this.flippedX )
        {
            g_bulletArray[g_bulletCount] = new Bullet( this.x, this.y, "left" , "e");
            g_sharedGameLayer.addBullet( g_bulletArray[g_bulletCount] );
        }
        else
        {
            g_bulletArray[g_bulletCount] = new Bullet(this.x,this.y,"right", "e");
            g_sharedGameLayer.addBullet( g_bulletArray[g_bulletCount] );
        }

        g_bulletCount++    

    },

    attack : function ()
    {
        this.flippedX = true;
        this.intializeBullet();
    },
    
    closeTo: function( obj , length ) 
    {
        var myPos = this.getPosition();
        var oPos = obj.getPosition();
        return ( ( Math.abs( myPos.x - oPos.x ) <= length )  );
    },

    handleCollision : function ()
    {

        g_bulletArray.forEach( function (b )
        {
            if ( this.closeTo( b , 5 ) && b.getTag() == "p" )
            {
                g_sharedGameLayer.removeChild(this);
                g_sharedGameLayer.removeChild(b);
            }
           
                
        },this);
    },
/**
    handleCollision: function( oldRect, newRect ) 
    {
        if ( this.ground ) {
            if ( !this.ground.onTop( newRect ) ) 
            {
                this.ground = null;
            }
        } 
        else 
        {
            if ( this.vy <= 0 ) 
            {
                var topBlock = this.findTopBlock( this.blocks,
                                                  oldRect,
                                                  newRect );
                
                if ( topBlock ) 
                {
                    this.ground = topBlock;
                   
                    this.vy = 0;
                }
            }
        }

        


    },*/
    
    getEnemyRect: function() 
    {
        var spriteRect = this.getBoundingBoxToWorld();
        var spritePos = this.getPosition();

        var dX = this.x - spritePos.x;
        var dY = this.y - spritePos.y;
        return cc.rect( spriteRect.x + dX,
                        spriteRect.y + dY,
                        spriteRect.width,
                        spriteRect.height );
    },

    findTopBlock: function( blocks, oldRect, newRect ) 
    {
        var topBlock = null;
        var topBlockY = -1;
        
        blocks.forEach( function( b ) 
        {
            if ( b.hitTop( oldRect, newRect ) ) 
            {
                if ( b.getTopY() > topBlockY ) 
                {
                    topBlockY = b.getTopY();
                    topBlock = b;
                }
            }
        }, this );
        
        return topBlock;
    },
    
    setBlocks: function( blocks ) 
    {
        this.blocks = blocks;
    }
});


        
