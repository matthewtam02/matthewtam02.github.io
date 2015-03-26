<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "matthewtam02@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "374391" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'2FCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQx1CHVqRxUSmiDQwOgRMdUASC2gVaWBtEAgIQNYNFmN0EEF237SpYUtXrcyahuy+ABR1YAjiAcVCQ5Dd0gASE0RRJ9IAcksgilhoKJAX6ogiNlDhR0WIxX0Amy7KtS4BJyQAAAAASUVORK5CYII=',
			'092F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2gVaXRAiIGdFLV06dKslZmhWUjuC2hlDHRoZUTTy9DoMIURzQ6WRocAVDGwWxxQxUBuZg1FdctAhR8VIRb3AQAz0sjM2Ve4DQAAAABJRU5ErkJggg==',
			'9E4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMdkMREpog0MLQ6OgQgiQW0AsWmOjqIoIsFwtWBnTRt6tSwlZmZoVlI7mN1FWlgbUQ1jwGolzU0EMU8AZB5jah2gN2Cphebmwcq/KgIsbgPAH9hy5FWG9LXAAAAAElFTkSuQmCC',
			'C5BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGUNDkMREWkUaWBsdHZDVBTQCxRoCUcUaREKQ1IGdFLVq6tKloStDs5DcF9DA0OiKbh5IDN28RhEMMZFW1lZ0t7CGMIYA3YwiNlDhR0WIxX0A29LLDpjsNssAAAAASUVORK5CYII=',
			'BEA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMEx1QBILmCLSwBDKEBCALNYq0sDo6OgggKaOtSHQAdl9oVFTw5auikzNQnIfVB2GeayhgQ4i6GINaGJgvQEoekFuBoqhuHmgwo+KEIv7AKJ/zZrPyYL4AAAAAElFTkSuQmCC',
			'6F86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoEWlgbQh0EEAWawCpc3RAdl9k1NSwVaErU7OQ3BcCNs8R1bxWiHkiBMSwuYU1AKgCzc0DFX5UhFjcBwCorcvSm6kvaQAAAABJRU5ErkJggg==',
			'26D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMRRYLaBVpZG0ICEXR3SrSABSD6YW4adq0sKWropaiuC9AtBVJHRgyOog0uqKJsTZgigFtALkFRSw0FOzm0IBBEH5UhFjcBwA9nMxbZLNxYwAAAABJRU5ErkJggg==',
			'E15C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHaYGIIkFNDAGsDYwBIigiLECxRgdWFDEgHqnMjoguy80alXU0szMLGT3gdQxNAQ6MKDpxSbGChRDt4PR0QHFLaEhrKEMoQwobh6o8KMixOI+ADumydThIMnDAAAAAElFTkSuQmCC',
			'7608' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMEx1QBZtZW1lCGUICEARE2lkdHR0EEEWmyLSwNoQAFMHcVPUtLClq6KmZiG5j9FBtBVJHRiyNog0ujYEopgnAhRzRLMjoAHTLQENWNw8QOFHRYjFfQB/IsvOOyRNngAAAABJRU5ErkJggg==',
			'CCE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDHVqRxURaWRtdGximOiCJBTSKNADFAgKQxRpEGlgbGB1EkNwXtWraqqWhK7OmIbkPTR1uMSx2YHMLNjcPVPhREWJxHwCmcMyON/TJEAAAAABJRU5ErkJggg==',
			'0897' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dGkSQxESmiDS6NgSgiAW0srayAsUCkNwXtXRl2MrMqJVZSO4DqWMICWhlQNEr0ujQEDCFAc0Ox4aAAAYMtzg6YHEzithAhR8VIRb3AQA/rcs+3sVS8AAAAABJRU5ErkJggg==',
			'7FFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA6YGIIu2ijSwNjAEiGCIMTqwIItNgYihuC9qatjS0JVZyO5jdEBRB4asDZhiIg2YdgQ0YLoFKobq5gEKPypCLO4DAKVYyi6mkK/NAAAAAElFTkSuQmCC',
			'61F3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0IdkMREpjAGsDYwOgQgiQW0sALFgHLIYg0MYLEAJPdFRq2KWhq6amkWkvtCpqCog+htZcA0D4uYCFgvqluALgkFqkNx80CFHxUhFvcBAMdwyj/lNfuwAAAAAElFTkSuQmCC',
			'6BF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDAxoCkMREpoi0sjYwNCKLBbSINLo2MLSiiDWA1U0JQHJfZNTUsKWhq6KikNwXAjaP0QFFbyvIPMbQEAwxBmxuQREDuxlNbKDCj4oQi/sAa1PN/elCpX0AAAAASUVORK5CYII=',
			'B4BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYWllDGVqRxQKmMExlbXSY6oAs1soQytoQEBCAoo7RlbXR0UEEyX2hUUuXLg1dmTUNyX0BU0RakdRBzRMNdW0IDA1BtaOVtSEQVd0UBgy9EDczoogNVPhREWJxHwDQBc1fi2igTwAAAABJRU5ErkJggg==',
			'CEF9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WENEQ1lDA6Y6IImJtIo0sDYwBAQgiQU0gsQYHUSQxRpQxMBOilo1NWxp6KqoMCT3QdQxTMXUC7QLww4GFDuwuQXsZqB5yG4eqPCjIsTiPgAjm8tbnfO3XwAAAABJRU5ErkJggg==',
			'F472' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QMQ6AIAxFPwM3wPt0ca+JXThNHXoDOQILpxQ3EEdN6E86vLTpS1GGUsyUX/xEYF44UcNYkWpn7plANwodcysO0tD4Scy55FJi48caDOc92e4uQgxDf8Mc1ckH8woemZN9gv99mBe/C+5MzV4Or+NQAAAAAElFTkSuQmCC',
			'5051' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHVqRxQIaGENYGximooqxtgLFQpHFAgNEGl2nMsD0gp0UNm3aytTMrKUo7msVaXRoCECxA5tYQCvIDlQxkSmMIYyOqO5jDWAIALokNGAQhB8VIRb3AQDWwsvXZC0jfwAAAABJRU5ErkJggg==',
			'4485' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QMQ6AIAxF24Eb4H1gYK+JXThNF24A3sBBTqlsBR01oX976U9fCvUxAjPlH78MCRiZNNugoPdO7+EGbGTtmMkY7r3glN++H0flM0blR9km9E6s6jIvHIQ61lzajZHdXaKBAUNxM/zvu7z4Xb1lypWbzsLrAAAAAElFTkSuQmCC',
			'90ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjCGsDYwOgQgiQW0sraCxERQxEQaXRFiYCdNmzptZWroyqxpSO5jdUVRB4GtmGICWOzA5hZsbh6o8KMixOI+AElHycsoQbfzAAAAAElFTkSuQmCC',
			'59A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMEx1QBILaGBtZQhlCAhAERNpdHR0dBBBEgsMEGl0bQiAqQM7KWza0qWpq6KmZiG7r5UxEEkdVIyh0TU0EMW8gFYWoHmoYiJTWFtZ0fSyBjCGAMVQ3DxQ4UdFiMV9ANlTzZbweglOAAAAAElFTkSuQmCC',
			'51D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGVqRxQIaGANYGx2mooqxBrA2BIQiiwUGMIDEYHrBTgqbtipqKQghu68VRR1OsQAsYiJTGEBuQREDuiQU6ObQgEEQflSEWNwHALM9yu9WVqQLAAAAAElFTkSuQmCC',
			'1D14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMDQEIImxOoi0MoQwNCKLiTqINDqGMLQGoOgVaXSYwjAlAMl9K7OmAdGqqCgk90HUMTpg6mUMDcE0rwFNXSu6+0RDREMYQx1QxAYq/KgIsbgPAAo0y23nw1M/AAAAAElFTkSuQmCC',
			'4BF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpI37poiGsIYGtKKIhYi0sjYwTEUWYwwRaXRtYAhFFmOdAlYH0wt20rRpU8OWhq5aiuy+AFR1YBgaCjYP1d4pWMUw9ILdDHRLwGAIP+pBLO4DAFsEy6kthfyxAAAAAElFTkSuQmCC',
			'E949' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHaY6IIkFNLC2MrQ6BASgiIkAVTk6iKCLBcLFwE4KjVq6NDMzKyoMyX0BDYyBrkDdqHoZGl1DgSagiLE0OjQ6oNkBdEsjqluwuXmgwo+KEIv7APj0znaxoE5YAAAAAElFTkSuQmCC',
			'DA6F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUNDkMQCpjCGMDo6OiCrC2hlbWVtQBcTaXRtYISJgZ0UtXTaytSpK0OzkNwHVodhnmioa0MgFvPQxKaINDqi6Q0NEGl0CGVEERuo8KMixOI+ALtyzAJJseIuAAAAAElFTkSuQmCC',
			'493E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjCGMIYyhgYgi4WwtrI2Ojogq2MMEWl0aAhEEWOdAhRDqAM7adq0pUuzpq4MzUJyX8AUxkAHNPNCQxkwzGOYwoJFDNMtWN08UOFHPYjFfQCdIssFfRJTfgAAAABJRU5ErkJggg==',
			'AFC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHaY6IImxBogAxQMCApDERKaINLA2CDqIIIkFtILEGGFiYCdFLZ0athRIhSG5D6KOYSqy3tBQsFgDpnkCGHaguwUkxoDm5oEKPypCLO4DAA+ozGPsmcaZAAAAAElFTkSuQmCC',
			'7C2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGaYGIIu2sjY6OjoEiKCIiTS4NgQ6sCCLTQGpCHRAcV/UtFWrVmZmIbuP0QGorpXRAdle1gag2BRUMREgdAhgRLEjoAHoFgcGFLcENDCGsoYGoLp5gMKPihCL+wCWK8rs/sK2OAAAAABJRU5ErkJggg==',
			'F7EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUMdkMQCGhgaXRsYHQKwiImgirWyItSBnRQatWra0tCVoVlI7gPKB7BimMfowIphHmsDpphIA6ZeoBiamwcq/KgIsbgPAA+Qy/tIHfRUAAAAAElFTkSuQmCC',
			'0F46' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHaY6IImxBog0MLQ6BAQgiYlMAYpNdXQQQBILaAWKBTo6ILsvaunUsJWZmalZSO4DqWNtdEQxDywWGugggm5HoyOKGNgtjahuYXQAi6G4eaDCj4oQi/sAHdXMKmbDwfEAAAAASUVORK5CYII=',
			'BD4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHUNDkMQCpoi0MrQ6OiCrC2gVaXSYiiY2BSgWCBcDOyk0atrKzMzM0Cwk94HUuTZimucaGohpB7o6kFvQxKBuRhEbqPCjIsTiPgBld80KYrbLpwAAAABJRU5ErkJggg==',
			'9CB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDGRoCkMREprA2ujY6NCKLBbSKNLgCSXQx1kaHKQFI7ps2ddqqpaGroqKQ3MfqClLn6ICslwGktyEwNARJTABiBza3oIhhc/NAhR8VIRb3AQDGUM8CBoUA4QAAAABJRU5ErkJggg==',
			'0988' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWl0bQh0EEESC2gVaXREqAM7KWrp0qVZoaumZiG5L6CVMdARzbyAVgYM80SmsGCIYXMLNjcPVPhREWJxHwCyjsvRyfxP0QAAAABJRU5ErkJggg==',
			'05A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRGAIAxFk4INcB8s7MMdaZgmFtkAR6BhSikDWuppfvfu5/Iu0C4j8Ke84odhYSggZJgjL8CwW+aLF1yDWkbqkxMqZPxyPWptOWfjRwr7JjGMu51x5DTe6D2aXJy6iWHANLOv/vdgbvxOdgvOT0kf2ioAAAAASUVORK5CYII=',
			'B835' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUMDkMQCprC2sjY6OiCrC2gVaXRoCEQVA6pjaHR0dUByX2jUyrBVU1dGRSG5D6LOoUEEw7wALGKBDiIYbnEIQHYfxM0MUx0GQfhREWJxHwA/Ac4JRLbt6AAAAABJRU5ErkJggg==',
			'9395' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUMDkMREpoi0Mjo6OiCrC2hlaHRtCEQXa2VtCHR1QHLftKmrwlZmRkZFIbmP1ZWhlSEkoEEE2WageQ4NqGICQDFHoB0iGG5xCEB2H8TNDFMdBkH4URFicR8Am2jLCwz/S5AAAAAASUVORK5CYII=',
			'6BB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGRoCkMREpoi0sjY6NCKLBbSINLo2BLSiiDWA1U0JQHJfZNTUsKWhq6KikNwXAjbP0QFFbyvIvMDQEAyxAGxuQRHD5uaBCj8qQizuAwAuSs92WFffzQAAAABJRU5ErkJggg==',
			'4180' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37pjAEMIQytKKIhTAGMDo6THVAEmMMYQ1gbQgICEASYwXqZXR0dBBBct+0aauiVoWuzJqG5L4AVHVgGBrKADQvEEUM5BZ0OxjAelHdwjCFNRTDzQMVftSDWNwHAFwAyUfG/BtHAAAAAElFTkSuQmCC',
			'FC98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGaY6IIkFNLA2Ojo6BASgiIk0uDYEOoigibE2BMDUgZ0UGjVt1crMqKlZSO4DqWMICcAwjwGLeY4YYtjcgunmgQo/KkIs7gMApiDOMaD/+9QAAAAASUVORK5CYII=',
			'38C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYQxhCHaY6IIkFTGFtZXQICAhAVtkq0ujaIOgggiwGVMcKpEWQ3LcyamXYUiAdhew+iLpGBwzzGFoZMMQEpjBgcQummx1DQwZB+FERYnEfACSvzBuFyorrAAAAAElFTkSuQmCC',
			'ECD4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDGRoCkMQCGlgbXRsdGlHFRBpcGwJa0cVYGwKmBCC5LzRq2qqlq6KiopDcB1EX6ICpNzA0BNMObG5BEcPm5oEKPypCLO4DALdR0KOHhm01AAAAAElFTkSuQmCC',
			'81EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUNDkMREpjAGsDYwOiCrC2hlxRATmcKALAZ20tKoVVFLQ1eGZiG5D00d1DzixLDpBbokFOhmFLGBCj8qQizuAwCyB8biu8VQywAAAABJRU5ErkJggg==',
			'F58A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGVqRxQIaRBoYHR2mOqCJsTYEBASgioUwOjo6iCC5LzRq6tJVoSuzpiG5D6in0RGhDi7m2hAYGoJqHkgMTR1rKyOGXsYQhlBGFLGBCj8qQizuAwDpAMy0SnQuawAAAABJRU5ErkJggg==',
			'F0F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA6Y6IIkFNDCGsDYwBASgiLG2sjYwOoigiIk0ugJpEST3hUZNW5kaumpVFJL7oOoaHTD1tjJg2MEwhQGLW1DFgG5uYAwNGQThR0WIxX0A3FTMrO0xqGoAAAAASUVORK5CYII=',
			'5222' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoggiQUGMDQ6gGSQ3Bc2bdXSVSuzVkUhu6+VYQoQNyLbAeQHgEWR7WhldACLIomJTGFtAIsiibEGiIa6hgaGhgyC8KMixOI+ACeAy9jVMMJwAAAAAElFTkSuQmCC',
			'8E7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6YGIImJTBEBkgEBIkhiAa0gXqADC7q6RkcHZPctjZoatmrpyixk94HVTWF0YEA3LwBTjNGBEcMOVqBKZLeA3dzAgOLmgQo/KkIs7gMAAc7LAyKMmV4AAAAASUVORK5CYII=',
			'6396' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoYWh0bQh0EEAWa2BoZQWKIbsvMmpV2MrMyNQsJPeFTGFoZQgJRDWvlaHRAahXBE3MEU0Mm1uwuXmgwo+KEIv7ANd7y/18BmQwAAAAAElFTkSuQmCC',
			'9620' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaWRtCAgIQBUDkoEOIkjumzZ1WtiqlZlZ05Dcx+oq2srQyghTB4FA8xymoIoJgMQCGFDsALvFgQHFLSA3s4YGoLh5oMKPihCL+wDk3MsUNc03lAAAAABJRU5ErkJggg==',
			'9B0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQximMEwNQBITmSLSyhDKECCCJBbQKtLo6OjowIIq1sraEOiA7L5pU6eGLV0VmYXsPlZXFHUQCDTPFU1MAIsd2NyCzc0DFX5UhFjcBwDWmssOVy9zpwAAAABJRU5ErkJggg==',
			'B8E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHRoCkMQCprC2sjYwNKKItYo0ujYwtGJRNyUAyX2hUSvDloauiopCch9EHaMDpnmMoSGYdmBzC4oYNjcPVPhREWJxHwB0sM7XzRBqAQAAAABJRU5ErkJggg==',
			'812D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sgawNgQ6iKCoA+pFiIGdtDRqVdSqlZlZ05DcB1bXyoiiN6AVKDYFi1gAI4YdjA6MKG4BuiSUNTQQxc0DFX5UhFjcBwCcUch6mS11DgAAAABJRU5ErkJggg==',
			'D77B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA0MdkMQCpjA0OjQEOgQgi7VCxERQxYCijjB1YCdFLV01bdXSlaFZSO4DqgtgmMKIZh6jA0MAI5p5rA2MDmhiU0QaQKLIekMDwGIobh6o8KMixOI+AMxCzRRtfuJ2AAAAAElFTkSuQmCC',
			'20E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUIdkMREpjCGsDYwOgQgiQW0srayguSQdbeKNLqC5JDdN23aytTQVUuzkN0XgKIODBkdIGLI5rE2YNoh0oDpltBQTDcPVPhREWJxHwAG+8tEbtSXXAAAAABJRU5ErkJggg==',
			'E5CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHaYGIIkFNIg0MDoEBIigibE2CDqwoIqFsAJVIrsvNGrq0qWrVmYhuw9odqMrQh0eMRGgGLodrK3obgkNYQxBd/NAhR8VIRb3AQBYJcxWST7nEgAAAABJRU5ErkJggg==',
			'71CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHVpRRFsZAxgdAqY6oIixBrA2CAQEIItNYQCKMTqIILsvalXU0lUrs6YhuY/RAUUdGLI2gMVCQ5DERMBigijqgPYB3RKIJsYayhDqiCI2UOFHRYjFfQD7y8i/z+bZGgAAAABJRU5ErkJggg==',
			'201D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIY6IImJTGEMYQhhdAhAEgtoZW1lBIqJIOtuFWl0mAIXg7hp2rSVWSCE7L4AFHVgCORhiLE2sLYyoImJNADdMgXVLaGhDAGMoY4obh6o8KMixOI+AOthyal2TY5AAAAAAElFTkSuQmCC',
			'6209' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QQQqAIBCF4efCG9h9xkX7CTTI00yQN7AjtPGURRBotSxqZveD+DHIlxH8aV/xaVYOCTMVzSQd4cFcNJ7MaK0lUzbB2Ep3tJ00hLwsOYS+8LmEpIXn6m0Eb03qpkhZqv7YLHK2aG48ncxf3e/BvfGtC03MGQnWb0sAAAAASUVORK5CYII=',
			'896F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6OiCrC2gVaXRtQBUTmQISY4SJgZ20NGrp0tSpK0OzkNwnMoUx0BXDPAag3kA0MRYMMWxugboZRWygwo+KEIv7ALJpyhl6NF+RAAAAAElFTkSuQmCC',
			'4B67' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQyhoYgi4WItDI6OjSIIIkxhog0ujagirFOEWllBdIBSO6bNm1q2NKpq1ZmIbkvAKTO0aEV2d7QUJB5AVNQ3QIWC0ATA7rF0QGLm1HFBir8qAexuA8A6S/L49daBQ0AAAAASUVORK5CYII=',
			'6085' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hhbWVtCEQVaxBpdHR0dHVAcl9k1LSVWaEro6KQ3BcyBaTOoUEEWW+rSKMryAQUMYgdIhhucQhAdh/EzQxTHQZB+FERYnEfALAFyzeC/V50AAAAAElFTkSuQmCC',
			'74F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZWllDA6Y6IIu2MkxlbWAICEAVC2VtYHQQQRabwugKVNcgguy+qKVLl4YCKST3AXW1AtU1ItvB2iAa6goyFUkMaA5I3RRksQCIWACmGGNoyCAIPypCLO4DALh6yw4qbQT5AAAAAElFTkSuQmCC',
			'B091' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMRRFrZW1lbQgIRVUn0ugKlEF2X2jUtJWZmVFLkd0HUucQEoBqRytQrAFdjLWVEV0M4hYUMaibQwMGQfhREWJxHwBgDs00+56rEAAAAABJRU5ErkJggg==',
			'417F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjAEsIYGhoYgi4UwBjA0BDogq2MMYcUQYwXqZWh0hImBnTRt2qqoVUtXhmYhuS8ApG4KI4re0FCgWACqGMgtjA6YYqwN6GKsoRhiAxV+1INY3AcAK8THHk5BsK4AAAAASUVORK5CYII=',
			'65EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUMDkMREpog0sDYwOiCrC2jBItYgEoIkBnZSZNTUpUtDV4ZmIbkvZApDoyu63lZsYiIYYiJTWFvR7WUNYAxBd/NAhR8VIRb3AQBC9snkunfeLgAAAABJRU5ErkJggg==',
			'CFFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7WENEQ11DA0MDkMREWkUaWBsYHZDVBTRiEWtAEQM7KWrV1LCloStDs5Dch6YOtxgWO7C5hTUELIbi5oEKPypCLO4DAD2QycEct2kyAAAAAElFTkSuQmCC',
			'863F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6OiCrC2gVaWRoCEQRE5ki0sCAUAd20tKoaWGrpq4MzUJyn8gU0VYGLOY5oJmHTQybW6BuRhEbqPCjIsTiPgBX3cqtYzgKWgAAAABJRU5ErkJggg==',
			'B2E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHaY6IIkFTGFtZW1gCAhAFmsVaXRtYHQQQFHHABZDdl9o1KqlS0NXpmYhuQ+obgprAyOaeQwBQDEHERQxRgcMMaBOdLeEBoiGuqK5eaDCj4oQi/sAlxnMezcGzS8AAAAASUVORK5CYII=',
			'5429' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2Quw2AMAxEL0U2CPuYgt5ImIIRmCKNNyBsQAFT8mlwBCUIfN2TrXsylstE/Cmv+EkDhSCRYRyRXEnMORMfawqG1ewqnOxQasdpWua+a62fBt1akr2FFkIDomW8bzGyjjBAHSFz8Qz1wpnzV/97MDd+KxPRyzCBZX5cAAAAAElFTkSuQmCC',
			'2FFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0MDkMREpog0sDYwOiCrC2jFFGNAFYO4adrUsKWhK0OzkN0XgKmX0QFTjLUBU0wEi1hoKFgMxc0DFX5UhFjcBwDtcciyiC41iwAAAABJRU5ErkJggg==',
			'B57C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6YGIIkFTBEBkQEiyGKtIF6gAwuquhCGRkcHZPeFRk1dumrpyixk9wVMYWh0mMLowIBiHlAsAF1MBGgaI5odrK2sDQwobgkNYAwBiqG4eaDCj4oQi/sAUyLM4qAe7DAAAAAASUVORK5CYII=',
			'B80D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYQximMIY6IIkFTGFtZQhldAhAFmsVaXR0dHQQQVPH2hAIEwM7KTRqZdjSVZFZ05Dch6YObp4rFjFsdqC7BZubByr8qAixuA8ApvPMl2dGT1AAAAAASUVORK5CYII=',
			'7E17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMIaGIIu2ijQwhDA0iKCJMaKLTQHypjA0BCC7L2pq2Kppq1ZmIbmP0QGsrhXZXtYGsNgUZDERiFgAslgAWIzRAVVMNJQx1BFFbKDCj4oQi/sASiDKnc8HlPgAAAAASUVORK5CYII=',
			'970B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTGFodAhldAhAEgtoZWh0dHR0EEEVa2VtCISpAztp2tRV05auigzNQnIfqytDAJI6CGxldACJIZsnADSNEc0OkSlAHppbWAOAYmhuHqjwoyLE4j4AxjbK2AIsUSsAAAAASUVORK5CYII=',
			'8478' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYWllDA6Y6IImJTGGYytAQEBCAJBbQyhDK0BDoIIKijtGVodEBpg7spKVRS5euWrpqahaS+0SmiLQyTGFAM0801CGAEcU8oB2tjA6MaHYA3deAqhfs5gYGFDcPVPhREWJxHwB+sMw/Yl++vAAAAABJRU5ErkJggg==',
			'25F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6Y6IImJTBFpYG1gCAhAEgtoBYkxOogg624VCUFSB3HTtKlLl4aumpqF7L4AhkZXNPMYHUBiqOaxNohgiAFtbUV3S2goI8heFDcPVPhREWJxHwAH5ss+NmC6QwAAAABJRU5ErkJggg==',
			'6E8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMdkMREpog0MDo6OgQgiQW0iDSwNgQ6iCCLNaCoAzspMmpq2KrQlaFZSO4LwWZeKxbzsIhhcws2Nw9U+FERYnEfAKVJywDYbzISAAAAAElFTkSuQmCC',
			'B234' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGRoCkMQCprC2sjY6NKKItYo0OgBJVHUMQFUOUwKQ3BcatWrpqqmroqKQ3AdUB1Tp6IBqHkMAQ0NgaAiKGKMDyCVobmlgBdmM4mbRUEc0Nw9U+FERYnEfAEEW0Dfrv0WnAAAAAElFTkSuQmCC',
			'A5ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMdkMRYA0QaWIEyAUhiIlMgYiJIYgGtIiFIYmAnRS2dunRp6MqsaUjuC2hlaHRF0xsaiikGNA+LGGsrulsCWhlD0N08UOFHRYjFfQCRP8s2rNzEpwAAAABJRU5ErkJggg==',
			'DEE2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaY6IIkFTBFpYG1gCAhAFmsFiTE6iGCIMTSIILkvaunUsKWhQBrJfVB1jQ6YelsZMMWmMGBxC6abHUNDBkH4URFicR8ATYHNAW5HevsAAAAASUVORK5CYII=',
			'B271' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA1qRxQKmsAL5AVNRxFpFGh0aAkJR1TE0OjQ6wPSCnRQatWopGCK5D6gOBFHtaGUIAEI0MUYHRgcGdLc0sDagioUGiIa6NjCEBgyC8KMixOI+AELEzZqieDS9AAAAAElFTkSuQmCC',
			'495E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjCGsIY6hgYgi4WwtrI2MDogq2MMEWl0RRNjnQIUmwoXAztp2rSlS1MzM0OzkNwXMIUx0KEhEEVvaChDI7oYwxQWoB3oYqytjI6OaGKMIQyhjKhuHqjwox7E4j4ASY/J9LjtL2oAAAAASUVORK5CYII=',
			'989E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQTY21lRYiBnTRt6sqwlZmRoVlI7mN1ZW1lCEHVywA0zwHNPAGgmCOaGDa3YHPzQIUfFSEW9wEAFsfJqJHfzrgAAAAASUVORK5CYII=',
			'2377' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANYQ1hDA0NDkMREpoi0MjQENIggiQW0MjQ6oIkxtDJARZHcN21V2Kqlq1ZmIbsvAKhuChAj6WV0AOoMAIoiu6WBodHRAaga2S0NIq2sINVIYqGhQDejiQ1U+FERYnEfAJMMyzrUJitIAAAAAElFTkSuQmCC',
			'828F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6OiCrC2gVaXRtCEQRE5nC0OiIUAd20tKoVUtXha4MzUJyH1DdFEzzGAJY0cwLaGV0QBcDuqUBXS9rgGioQygjithAhR8VIRb3AQDwcMl1ofyUpwAAAABJRU5ErkJggg==',
			'4365' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37prCGMIQyhgYgi4WItDI6Ojogq2MMYWh0bUAVY53C0MrawOjqgOS+adNWhS2dujIqCsl9ASB1jg4NIkh6Q0NB5gWgiDFMAYkFOqCKgdziEIDiPrCbGaY6DIbwox7E4j4AevbLJ93/vN8AAAAASUVORK5CYII=',
			'97FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0MdkMREpjA0ujYwOgQgiQW0QsREUMVaWRHqwE6aNnXVtKWhK0OzkNzH6soQwIpmHkMrowMrmnkCQNPQxUSmiDSg62UNAIuhuHmgwo+KEIv7AC2aylCzc6ugAAAAAElFTkSuQmCC',
			'C340' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WENYQxgaHVqRxURaRVoZWh2mOiCJBTQCVU11CAhAFmtgaGUIdHQQQXJf1KpVYSszM7OmIbkPpI61Ea4OJtboGhqIKgayoxHVDrBbGlHdgs3NAxV+VIRY3AcAyeHNgxm7pZ8AAAAASUVORK5CYII=',
			'52FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA0MdkMQCGlhbWRsYHQJQxEQaXYFiIkhigQEMyGJgJ4VNW7V0aejKrGnI7mtlmMKKphcoFoAuFtDK6IAuJgLUie4W1gDRUKC9KG4eqPCjIsTiPgBffcqBLsCMagAAAABJRU5ErkJggg==',
			'E385' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGUMDkMQCGkRaGR0dHRhQxBgaXRsC0cVA6lwdkNwXGrUqbFXoyqgoJPdB1Dk0iGCYF4BFLNBBBMMtDgHI7oO4mWGqwyAIPypCLO4DAB0izFr4gk6MAAAAAElFTkSuQmCC',
			'5417' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMIaGIIkFNDBMZQhhaBBBFQtlRBMLDGB0ZZgCloO7L2za0qWrpq1amYXsvlYRoB1Ae5BtbhUNdZgC0o1kRyvILQwByGIiU8Duc0AWYw1gaGUMdUQRG6jwoyLE4j4AFo3LDxzlktoAAAAASUVORK5CYII=',
			'2577' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0NDkMREpogAyYAGESSxgFZMMYZWkRCGRgegKJL7pk1dumrpqpVZyO4LAKqawtCKbC+jA1AsgGEKilsaRBodHYCqkd3SwNrKClKNJBYayhiCLjZQ4UdFiMV9ANSby3OpVbZcAAAAAElFTkSuQmCC',
			'EF4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHUMdkMQCGkQaGFodHQLQxaY6OoigiwXC1YGdFBo1NWxlZmZoFpL7QOpYGzHNYw0NxDSvEYsdaHpDQ8BiKG4eqPCjIsTiPgDPnM15SOCL9gAAAABJRU5ErkJggg==',
			'85B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUNDkMREpog0sDY6NIggiQW0AsUaAlDEgOpCQOoCkNy3NGrq0qWhq1ZmIblPZApDo2ujQysDinlAsYaAKahiIiCxAAYUO1hbWRsdHVDdzBgCdDOK2ECFHxUhFvcBAHlzzQytcyiDAAAAAElFTkSuQmCC',
			'1DF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6Y6IImxOoi0sjYwBAQgiYk6iDS6AlWLoOgFiTE0iCC5b2XWtJWpoatWRSG5D6qu0QFTbysDptgUNDGwW5DFREOAbm5gDA0ZBOFHRYjFfQAz7Mm/wHbQygAAAABJRU5ErkJggg==',
			'6E41' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQxkaHVqRxUSmiDQwtDpMRRYLaAGKTXUIRRFrAIoFwvWCnRQZNTVsZWbWUmT3hQDNY0WzI6AVKBYagCGG1S1oYlA3hwYMgvCjIsTiPgAtPs0QCokLewAAAABJRU5ErkJggg==',
			'A48B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUMdkMRYAximMjo6OgQgiYlMYQhlbQh0EEESC2hldEVSB3ZS1NKlS1eFrgzNQnJfQKtIK7p5oaGioa4Y5jG0YtrBgKEXJIbu5oEKPypCLO4DAIGWyzP5lBE1AAAAAElFTkSuQmCC',
			'C736' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WENEQx1DGaY6IImJtDI0ujY6BAQgiQU0MjQ6NAQ6CCCLNTAAVTo6ILsvatWqaaumrkzNQnIfUF0AUB2qeQ2MQH2BDiIodrA2oIuJtIo0sKK5hTVEpIERzc0DFX5UhFjcBwB2Rs0cpgLUOAAAAABJRU5ErkJggg==',
			'B8A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQximMIaGIIkFTGFtZQhlaBBBFmsVaXR0dEAVA6pjbQgAQoT7QqNWhi1dFbUyC8l9UHWtDGjmuYYGTMEQawgIYMCwI9AB3c3oYgMVflSEWNwHAKX1ziqdWyRMAAAAAElFTkSuQmCC',
			'5ABD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUMdkMQCGhhDWBsdHQJQxFhbWRsCHUSQxAIDRBpdgepEkNwXNm3aytTQlVnTkN3XiqIOKiYa6opmXgBIHZqYyBSIXmS3sILsRXPzQIUfFSEW9wEA16nM4/eXUjcAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>