<?php
/**
 * Miscellaneous settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Miscellaneous Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('misc'); ?>


<?php do_settings_sections('misc'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php include('./admin-footer.php'); ?>
