var enemySpeed = randomIntFromInterval(25,100);
var Speed = randomIntFromInterval(36,70);
$(document).keydown(function(e) {
    switch(e.which) {
        case 37: // left

        break;

        case 38: // up
        break;

        case 39: // right
        $('#one').animate({
        'marginLeft' : "+="+Speed+"px" //moves right
        });
        Speed = randomIntFromInterval(36,70);
        //alert (Speed);
     
        $('#two').animate({
        'marginLeft' : "+="+enemySpeed+"px" //moves right
        });
        enemySpeed = randomIntFromInterval(25,100);
        //alert (enemySpeed);
        break;

        case 40: // down
        break;

        default: return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
});

function randomIntFromInterval(min,max)
{
    return Math.floor(Math.random()*(max-min+1)+min);
}