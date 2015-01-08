// make UpdateXMovement update constantly
var screenWidth = 700 ;
var screenHeight = 216;

var g_sharedGameLayer;
var g_bulletArray ;
var g_bulletCount ;
var g_childArray ;
var g_player ;

var GameLayer = cc.LayerColor.extend(
{
    _bulletBatch : null,
    _maxX : 2126,
    _maxY : 260,

    init: function() {
        this._super( new cc.Color4B( 127, 127, 127, 0 ) , this.getMaxX() , this.getMaxY());
        this.setPosition( new cc.Point( 0, 0 ) );
        
        g_childArray = [];
        this.background = new Background(0,0);
        g_childArray.push(this.background);
        this.player = new Player( 400, 25, this);
        g_childArray.push(this.player);
        g_player = this.player;


        this.enemy = new Enemy(750,25);
        g_childArray.push(this.enemy);

        this.enemy = new Enemy(1000,25);
        g_childArray.push(this.enemy);

        this.enemy = new Enemy(1500,25);
        g_childArray.push(this.enemy);

        this.enemy = new Enemy(2000,25);
        g_childArray.push(this.enemy);
        var followAction = cc.Follow.create(this.player , cc.rect(0,0,2126,216));
        this.runAction(followAction);

        this.setKeyboardEnabled( true );
        this.addAllChild( this.childArray );
        this.scheduleUpdate();
        g_sharedGameLayer = this;
        g_bulletArray = [];
        g_bulletCount = 0;
        
        return true;
    },
    
    onKeyDown: function( e )
    {
        this.player.handleKeyDown( e );
    },

    onKeyUp : function ( e )
    {
        this.player.handleKeyUp( e );
    },

    createBlocks : function()
    {
        this.blocks = [];

        this.blocks.forEach( function (b )
        {
            this.addChild( b );    
        },this);
    },

    addAllChild : function( childArray)
    {
        g_childArray.forEach( function (b )
        {
            this.addChild( b );
            b.scheduleUpdate();    
        },this);
    },

    addBullet : function ( bullet )
    {
        this.addChild(bullet);
        bullet.scheduleUpdate();
    },

    getMaxX : function ()
    {
        return this._maxX;
    },

    getMaxY : function ()
    {
        return this._maxY;
    }
        

});

var StartScene = cc.Scene.extend(
{
    onEnter: function()  
    {
        this._super();

        var layer = new GameLayer();

        layer.init();
 
        this.addChild( layer );
    }
});


