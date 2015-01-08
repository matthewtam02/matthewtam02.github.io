var Player = cc.Sprite.extend({
    ctor: function( x, y ) 
    {
        this._super();
        this.initWithFile( 'images/marco.png' );

        this.x = x;
        this.y = y;
        this.g = -1;
        this.vx = 0;
        this.vy = 5;

        this._moveLeft = false;
        this._moveRight = false;
        this._jump = false;
        this._attack = false;
        this.flippedX = false;
        this.direction = "right";

        this.ground = null;
        this.blocks = [];

        this.bulletCount = 0;

        this.updateSpritePosition();
    },

    handleKeyDown : function ( e )
    {
        if ( Player.KEYMAP[ e ] != undefined ) 
            this[ Player.KEYMAP[ e ] ] = true;
        
    },

    handleKeyUp : function ( e )
    {
        if ( Player.KEYMAP[ e ] != undefined ) 
            this[ Player.KEYMAP[ e ] ] = false;
    },

    updateSpritePosition: function() 
    {
        this.setPosition( cc.p( Math.round( this.x ),
                                Math.round( this.y ) ) );
    },
    
    update: function( dt) 
    {

        var currentPositionRect = this.getPlayerRect();

        this.updateYMovement();
        
        this.updateXMovement();
        this.updateSpritePosition();
        this.attack();

        var newPositionRect = this.getPlayerRect();
        this.handleCollision( currentPositionRect,
                              newPositionRect );

        
    },

    getDirection : function ()
    {
        return this.direction;
    },

    updateXMovement: function() 
    {
            // add velocity and accerelation
            if ( this._moveRight ) 
            {
                this.x += 20 ;
                this.flippedX = false;
                this.setFlippedX(this.flippedX);
                this._moveRight = false;
                console.log("move right"+this.x);
            } 
            if( this._moveLeft)
            {
                this.x -= 20;
                this.flippedX = true;
                this.setFlippedX(this.flippedX);
                this._moveLeft = false;
                console.log("move left"+this.x);

            }


        
        
    },

    updateYMovement: function() 
    {
        if ( this.y <=  25 ) 
        {
            this.vy = 0;
            if ( this._jump ) 
            {
                this.vy = this.jumpV;
                this.y = 25 + this.vy;
                this.ground = null;
            }
        } 
        else 
        {
            this.vy += this.g;
            this.y += this.vy;
        }
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
            g_bulletArray[g_bulletCount] = new Bullet( this.x, this.y, "left" , "p");
            g_sharedGameLayer.addBullet( g_bulletArray[g_bulletCount] );
        }
        else
        {
            g_bulletArray[g_bulletCount] = new Bullet(this.x,this.y,"right", "p");
            g_sharedGameLayer.addBullet( g_bulletArray[g_bulletCount] );
        }

        g_bulletCount++    

    },

    attack : function ()
    {
        if( this._attack )       
        {
            console.log("attack");
            this.intializeBullet();
            this._attack = false;
        }
    },

    closeTo: function( obj ) 
    {
        var myPos = this.getPosition();
        var oPos = obj.getPosition();
        return ( ( Math.abs( myPos.x - oPos.x ) <= 49 ) &&
             ( Math.abs( myPos.y - oPos.y ) <= 50 ) );
    },

    //recode handle collision
    handleCollision : function( oldRect, newRect ) 
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

        g_bulletArray.forEach( function (b )
        {
            if ( this.closeTo( b , 5 ) && b.getTag() == "e" )
            {
                g_sharedGameLayer.removeChild(this);
                g_sharedGameLayer.removeChild(b);
            }
           
                
        },this);


    },
    
    getPlayerRect: function() 
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

Player.KEYMAP = {}
Player.KEYMAP[cc.KEY.left] = '_moveLeft';
Player.KEYMAP[cc.KEY.right] = '_moveRight';
Player.KEYMAP[cc.KEY.c] = "_attack"
Player.KEYMAP[cc.KEY.up] = '_jump';

        
