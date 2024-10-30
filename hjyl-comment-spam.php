<?php
/*
	Plugin Name: Hjyl Comment Spam
	Plugin URI: https://hjyl.org/hjyl-comment-spam
	Description: A simple Anti Spam for Comment by number or english.非常简单的数字或字母评论验证码。
	Version: 1.3
	Author: hjyl
	Author URI: https://hjyl.org
	License: GPLv3 or later
	License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/
define('HJYL_COMMENT_SPAM_URL', plugin_dir_url( __FILE__ ));
define('HJYL_COMMENT_SPAM_PATH', dirname( plugin_basename( __FILE__ ) ));

function hjyl_l10n(){
	load_plugin_textdomain( 'hjyl-comment-spam', false, HJYL_COMMENT_SPAM_PATH.'/languages/' );
}
add_action( 'plugins_loaded', 'hjyl_l10n' );
function hjyl_comment_spam(){
	wp_enqueue_style('comment', HJYL_COMMENT_SPAM_URL. 'hjyl-comment-spam.css', array(), '20191022', 'all', false);
}
add_action( 'wp_footer', 'hjyl_comment_spam' );
// ADD: Anti-spam Code
 function hjyl_antispam(){
	if(!is_user_logged_in()){
		if(get_option('hjyl_spam')){
			$pcodes = substr(md5(mt_rand(11111,99999)),0,4);//English+Number
		}else{
			$pcodes = substr(mt_rand(0,99999),0,4);	//Number
		}	
		$str = '<p id="hjyl_anti">';
		$str .= '<label for="subpcodes">'.__('Anti-spam Code','hjyl-comment-spam').':</label>';
		$str .= '<input type="text"  size="4" id="subpcodes" name="subpcodes" maxlength="4" />';
		$str .= '<span id="pcodes">'.$pcodes.'</span>';
		$str .= '<input type="hidden" value="'.$pcodes.'" name="pcodes" />';
		$str .= '</p>';
		echo $str;
	}
 }

 function yanzhengma(){
	if ( !is_user_logged_in() ) {
		$pcodes = trim($_POST['pcodes']);
		$subpcodes = trim($_POST['subpcodes']);
		if((($pcodes)!=$subpcodes) || empty($subpcodes)){
			hjyl_comment_err( __('Error: Incorrect Anti-spam Code!','hjyl-comment-spam') );
		}
	}
}
add_filter('pre_comment_on_post', 'yanzhengma');
add_action('comment_form', 'hjyl_antispam', 1, 1);
// ADD: for error
function hjyl_comment_err($a) { 
    header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

add_action('admin_menu', 'hjyl_add_pages');
// action function for above hook
function hjyl_add_pages() {
    // Add a new top-level menu (ill-advised):
    add_options_page(__('HJYL COMMENT SPAM','hjyl-comment-spam'), __('HJYL COMMENT SPAM','hjyl-comment-spam'), 'manage_options', 'hjyl-comment-spam', 'hjyl_settings_page' );
}
function hjyl_settings_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
?>
<?php
    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Hjyl Comment Spam Settings', 'hjyl-comment-spam' ) . "</h2>";

    // settings form
    
    ?>
<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div class="inner-sidebar">
		<div style="position:relative;" class="meta-box-sortabless ui-sortable" id="side-sortables">
			<div class="postbox" id="sm_pnres">
						<h3 class="hndle"><span><?php _e('Donation','hjyl-comment-spam'); ?></span></h3>
						<div class="inside" style="margin:0;padding-top:10px;background-color:#ffffe0;">
								<?php printf(__('Created, Developed and maintained by %s . If you feel my work is useful and want to support the development of more free resources, you can donate me. Thank you very much!','hjyl-comment-spam'), '<a href="'.esc_url( __( 'https://hjyl.org/', 'hjyl-comment-spam' ) ).'">HJYL</a>'); ?>
									<br /><br />
									<table>
									<tr>
									<form name="_xclick" action="<?php echo esc_url( __( 'https://www.paypal.com/cgi-bin/webscr', 'hjyl-comment-spam' ) ); ?>" method="post">
										<input type="hidden" name="cmd" value="_xclick">
										<input type="hidden" name="business" value="i@hjyl.org">
										<input type="hidden" name="item_name" value="hjyl WordPress Theme">
										<input type="hidden" name="charset" value="utf-8" >
										<input type="hidden" name="currency_code" value="USD">
										<input type="image" src="<?php echo esc_url( __( 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif', 'hjyl-comment-spam' ) ); ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
									</form>
									</tr>
									<tr>
									<img src="<?php echo esc_url( __( 'https://hilau.com/wp-content/uploads/2019/10/alipay.jpg', 'hjyl-comment-spam' ) ); ?>" alt="<?php _e('Alipay', 'hjyl-comment-spam'); ?>" />
									</tr>
									</table>
						</div>
				</div>
		</div>
	</div>
		<form action="options.php" method="post" name="hjyl_comment_spam_form">
		<?php wp_nonce_field('update-options'); ?>   
		<p>
		<input type="checkbox" name="hjyl_spam" id="hjyl_spam" value="1" <?php if(get_option('hjyl_spam')) echo 'checked="checked"';?>>
		<?php _e("Complex Spam include character", 'hjyl-comment-spam' ); ?> 
		<?php _e("( Default Number Spam if not checked )", 'hjyl-comment-spam' ); ?>
		</p>

		<p class="submit">
		    <input type="hidden" name="action" value="update" />   
            <input type="hidden" name="page_options" value="hjyl_spam" />   
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>

		</form>
</div>

<?php
 
}
?>