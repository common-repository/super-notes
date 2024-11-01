<?php

/**
 * Create and render "welcome" page
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'admin_menu', array( 'WDPSN_WelcomePage', 'register_menu_item' ) );
add_action( 'admin_menu', array( 'WDPSN_WelcomePage', 'register_welcome_subpage' ), 9 );
add_action( 'wp_ajax_wdpsn_install_dummy_data', array( 'WDPSN_WelcomePage', 'install_dummy_data' ) );
/**
 * Welcome page class
 */
abstract class WDPSN_WelcomePage
{
    /**
     * Register menu item
     */
    public static function register_menu_item()
    {
        $menu_page = add_menu_page(
            esc_html( __( 'Super Notes - Welcome', 'super-notes' ) ),
            esc_html( __( 'Super Notes', 'super-notes' ) ),
            'preview_own_wdpsn_notes',
            'wdpsn',
            array( get_class(), 'render_welcome_page' ),
            'dashicons-format-aside'
        );
    }
    
    /**
     * Register "welcome" page on first menu position
     */
    public static function register_welcome_subpage()
    {
        add_submenu_page(
            'wdpsn',
            esc_html( __( 'Super Notes - Welcome', 'super-notes' ) ),
            esc_html( __( 'Welcome', 'super-notes' ) ),
            'preview_own_wdpsn_notes',
            'wdpsn',
            array( get_class(), 'render_welcome_page' )
        );
    }
    
    /**
     * Render welcome page
     */
    public static function render_welcome_page()
    {
        $path = plugin_dir_url( WDPSN_MAIN_FILE ) . 'assets/img/';
        $plan = self::get_plugin_plan();
        ?>
		<div class="wrap wdpsn-welcome-page">

			<div class="wdpsn-welcome-page__heading"></div>
			<div class="wdpsn-welcome-page__container">

				<div class="wdpsn-welcome-page__container__content">

					<img src="<?php 
        echo  esc_url( $path . 'icon-256x256.png' ) ;
        ?>" alt="<?php 
        echo  esc_html( __( 'Super Notes', 'super-notes' ) ) ;
        ?>" class="wdpsn-welcome-page__container__content__icon" />

					<h1><?php 
        echo  esc_html( __( 'Thank you for using Super Notes!', 'super-notes' ) ) ;
        ?></h1>
					<p><?php 
        echo  esc_html( __( 'This plugin allows you to add custom notes to any posts, pages, users, media elements, plugins and even more different elements. It comes with great customization options, and allows you to configure custom rules, choose notes locations and user permissions for any type of note.', 'super-notes' ) ) ;
        ?></p>
					<p><?php 
        echo  esc_html( __( 'Super Notes plugin is developed and maintained by', 'super-notes' ) ) ;
        ?> <a href="http://wedoplugins.com/" target="_blank">We Do Plugins</a>.</p>

					<ul class="wdpsn-welcome-page__container__content__table-of-contents">
						<li><a href="#how-to-add-notes"><?php 
        echo  esc_html( __( 'How to add notes?', 'super-notes' ) ) ;
        ?></a></li>
						<li><a href="#what-are-note-types"><?php 
        echo  esc_html( __( 'What are "Note Types"?', 'super-notes' ) ) ;
        ?></a></li>
						<li><a href="#can-i-see-all-notes-summary"><?php 
        echo  esc_html( __( 'Can I see all notes summary?', 'super-notes' ) ) ;
        ?></a></li>
						<li><a href="#premium-features"><?php 
        echo  esc_html( __( 'Premium features', 'super-notes' ) ) ;
        ?></a></li>
						<li><a href="#thank-you"><?php 
        echo  esc_html( __( 'Thank you!', 'super-notes' ) ) ;
        ?></a></li>
					</ul>

					<h2 data-chapter="how-to-add-notes"><a href="#how-to-add-notes" name="how-to-add-notes"><span class="dashicons dashicons-admin-links"></span></a> <?php 
        echo  esc_html( __( 'How to add notes?', 'super-notes' ) ) ;
        ?></h2>

					<p><?php 
        echo  wp_kses( __( 'Before you can add notes, you\'ll need to create "Note Type". Depending on the plugin plan you use, you can add one Note Type in free plugin plan, and unlimited in Premium plan. <a href="#what-are-note-types">More about Note Types</a> can be found below.', 'super-notes' ), array(
            'a' => array(
            'href' => array(),
        ),
        ) ) ;
        ?></p>
					<p><?php 
        echo  esc_html( __( 'When your Note Type is correctly configured and published, you can add new note in the locations selected for this Note Type. Simply click on "Show" button near the element you want assign note to, and create your note.', 'super-notes' ) ) ;
        ?></p>

					<img src="<?php 
        echo  esc_url( $path . 'screenshot-01.jpg' ) ;
        ?>" alt="" />

					<p><?php 
        echo  esc_html( __( 'You can also add notes to global location, which means it will be always available after click on top admin bar element:', 'super-notes' ) ) ;
        ?></p>

					<img src="<?php 
        echo  esc_url( $path . 'screenshot-02.jpg' ) ;
        ?>" alt="" />

					<p><?php 
        echo  esc_html( __( 'And of course, you can manage notes assigned to single post, page and other elements in "Edit" screen of this element:', 'super-notes' ) ) ;
        ?></p>

					<img src="<?php 
        echo  esc_url( $path . 'screenshot-03.jpg' ) ;
        ?>" alt="" />

					<h2 data-chapter="what-are-note-types"><a href="#what-are-note-types" name="what-are-note-types"><span class="dashicons dashicons-admin-links"></span></a> <?php 
        echo  esc_html( __( 'What are "Note Types"?', 'super-notes' ) ) ;
        ?></h2>

					<p><?php 
        echo  esc_html( __( 'Note Types allows you to create different categories (types) for notes you want to use on your website.', 'super-notes' ) ) ;
        ?></p>

					<p><?php 
        echo  wp_kses( __( 'Let\'s suppose that you have some users with "Author" role that publish posts on your website. You can create "Authors Notes" Note Type, and assign all users with "Author" role to see notes in this Note Type. This gives you the possibility to <strong>display notes for specific users only</strong>.', 'super-notes' ), array(
            'strong' => array(),
        ) ) ;
        ?></p>
					<p><?php 
        echo  esc_html( __( 'Another example - let\'s say that you have dozens of plugins installed on your website. Some of them may require special attention, and it\'s important to not forget about some tasks every now and then. You can handle it with this plugin - simply create Note Type and configure "location" option to allow adding single notes to plugins only!', 'super-notes' ) ) ;
        ?></p>

					<img src="<?php 
        echo  esc_url( $path . 'screenshot-04.jpg' ) ;
        ?>" alt="" />

					<?php 
        /* Translators: Note Types page */
        ?>
					<p><?php 
        echo  wp_kses( sprintf( __( 'Custom users permissions and location rules can be combined, so you can create unlimited combinations of Note Types in premium plugin plan. Feel free to go to %s and play with different configuration options to find out what fits you well :)', 'super-notes' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=wdpsn_note_types' ) ) . '">' . esc_html( __( 'Note Types page', 'super-notes' ) ) . '</a>' ), array(
            'a' => array(
            'href' => array(),
        ),
        ) ) ;
        ?></p>

					<h2 data-chapter="can-i-see-all-notes-summary"><a href="#can-i-see-all-notes-summary" name="can-i-see-all-notes-summary"><span class="dashicons dashicons-admin-links"></span></a> <?php 
        echo  esc_html( __( 'Can I see all notes summary?', 'super-notes' ) ) ;
        ?></h2>

					<p><?php 
        echo  esc_html( __( 'Sure you can! :) Simply go to "Super Notes" - "All notes" page and preview all notes with "Viewer" role permission granted to you. This means that you will not see notes assigned to Note Type where you are not listed as "Viewer", even if you are website administrator.', 'super-notes' ) ) ;
        ?></p>

					<img src="<?php 
        echo  esc_url( $path . 'screenshot-05.jpg' ) ;
        ?>" alt="" />

					<h2 data-chapter="premium-features"><a href="#premium-features" name="premium-features"><span class="dashicons dashicons-admin-links"></span></a> <?php 
        echo  esc_html( __( 'Premium features', 'super-notes' ) ) ;
        ?></h2>

					<?php 
        self::render_premium_plan_notice( $plan );
        ?>

					<p><?php 
        echo  wp_kses( __( 'With premium plugin plan you\'re able to <strong>create unlimited Note Types</strong>, so any notes can be well organized and displayed only to proper users. But that\'s not the only premium feature! There\'s more:', 'super-notes' ), array(
            'strong' => array(),
        ) ) ;
        ?></p>
					<ul>
						<li>
							<strong><?php 
        echo  esc_html( __( 'Assign Tags to single notes', 'super-notes' ) ) ;
        ?></strong>
							<br> <?php 
        echo  esc_html( __( 'create unlimited tags and assign it to single notes', 'super-notes' ) ) ;
        ?>
							<img src="<?php 
        echo  esc_url( $path . 'screenshot-07.jpg' ) ;
        ?>" alt="" />
						</li>
						<li>
							<strong><?php 
        echo  esc_html( __( 'Notify note Owners and/or Viewers about note updates', 'super-notes' ) ) ;
        ?></strong>
							<br> <?php 
        echo  esc_html( __( 'Send a short email notification about any note content, settings or assigned tags changes, right to Owners and/or Viewers emails. Custom options for that can be configured for each notes separately.', 'super-notes' ) ) ;
        ?>
							<img src="<?php 
        echo  esc_url( $path . 'screenshot-06.jpg' ) ;
        ?>" alt="" />
						</li>
						<li>
							<strong><?php 
        echo  esc_html( __( 'Access to high-quality support', 'super-notes' ) ) ;
        ?></strong>
							<?php 
        /* Translators: Support e-mail address */
        ?>
							<br> <?php 
        echo  wp_kses( sprintf( __( 'With a valid premium license you\'re also getting an access to our premium support. Have any questions or problems with this plugin? Feel free to email us at %s, we\'ll help as fast as possible :)', 'super-notes' ), '<a href="mailto:support@wedoplugins.com">support@wedoplugins.com</a>' ), array(
            'a' => array(
            'href' => array(),
        ),
        ) ) ;
        ?>
						</li>
					</ul>

					<h2 data-chapter="thank-you"><a href="#thank-you" name="thank-you"><span class="dashicons dashicons-admin-links"></span></a> <?php 
        echo  esc_html( __( 'Thank you!', 'super-notes' ) ) ;
        ?></h2>
					<?php 
        /* Translators: Twitter account & contact e-mail address */
        ?>
					<p><?php 
        echo  wp_kses( sprintf( __( 'Huge thanks for using our plugin! We hope you like it - if you have any thoughts or feature ideas, feel free to share it with us. %1$s, %2$s or add a comment in plugin page on WordPress repository.', 'super-notes' ), '<a href="https://twitter.com/wedoplugins" target="_blank">' . esc_html( __( 'Find us on Twitter', 'super-notes' ) ) . '</a>', '<a href="mailto:hello@wedoplugins.com">' . esc_html( __( 'write an email', 'super-notes' ) ) . '</a>' ), array(
            'a' => array(
            'href' => array(),
        ),
        ) ) ;
        ?></p>

				</div>

				<div class="wdpsn-welcome-page__container__sidebar">

					<div class="wdpsn-welcome-page__container__sidebar__widget">
						<?php 
        echo  wp_kses( self::render_plugin_summary_widget( $plan ), array(
            'h3'     => array(),
            'p'      => array(),
            'br'     => array(),
            'strong' => array(),
            'a'      => array(
            'href' => array(),
        ),
        ) ) ;
        ?>
					</div>

					<?php 
        
        if ( current_user_can( 'manage_wdpsn_plugin' ) ) {
            ?>
						<div class="wdpsn-welcome-page__container__sidebar__widget">
							<h3><?php 
            echo  esc_html( __( 'Install dummy-data', 'super-notes' ) ) ;
            ?></h3>
							<p><?php 
            echo  esc_html( __( 'Click on the button bellow to install example Note Type and add one example note. This could help you getting started!', 'super-notes' ) ) ;
            ?></p>
							<button id="wdpsn-install-dummy-data" class="button button-secondary" type="button"><?php 
            echo  esc_html( __( 'Install now', 'super-notes' ) ) ;
            ?></button>
						</div>
						<?php 
        }
        
        ?>

					<div class="wdpsn-welcome-page__container__sidebar__widget wdpsn-welcome-page__container__sidebar__widget--promo">
						<img src="<?php 
        echo  esc_url( $path . 'logo-color-160x160.png' ) ;
        ?>" alt="<?php 
        echo  esc_html( __( 'We Do Plugins', 'super-notes' ) ) ;
        ?>" />
						<span><?php 
        echo  esc_html( __( 'We Do Plugins', 'super-notes' ) ) ;
        ?>, <?php 
        echo  esc_html( WDPSN_Helper::get_creation_year( '2018' ) ) ;
        ?><br> <a href="https://profiles.wordpress.org/wedoplugins/#content-plugins" target="_blank"><?php 
        echo  esc_html( __( 'Check our other plugins!', 'super-notes' ) ) ;
        ?></a></span>
					</div>

				</div>

			</div>

		</div>
		<?php 
    }
    
    /**
     * Get plugin plan
     */
    public static function get_plugin_plan()
    {
        $plan = 'free';
        return $plan;
    }
    
    /**
     * Render premium plan notice
     *
     * @param string $plan Current plan.
     */
    public static function render_premium_plan_notice( $plan )
    {
        $message = __( 'You\'re using free plugin plan now - premium features are <strong>not available</strong>. Upgrade now to get access to direct support and premium features!', 'super-notes' );
        ?>
		<div class="wdpsn-note-container wdpsn-note-container--<?php 
        echo  esc_attr( ( 'free' === $plan ? 'error' : 'success' ) ) ;
        ?>">
			<div class="wdpsn-note-container__content">
				<p><?php 
        echo  wp_kses( $message, array(
            'strong' => array(),
        ) ) ;
        ?></p>
			</div>
		</div>
		<?php 
    }
    
    /**
     * Render plugin plan summary
     *
     * @param string $plan Current plan.
     */
    public static function render_plugin_summary_widget( $plan )
    {
        $message = '<h3>' . esc_html( __( 'Summary', 'super-notes' ) ) . '</h3>';
        $message .= '<p>Plugin version: ' . esc_html( WDPSN_VERSION );
        switch ( $plan ) {
            case 'free':
                $message .= '<br>' . wp_kses( __( 'You\'re using <strong>free</strong> plugin plan.', 'super-notes' ), array(
                    'strong' => array(),
                ) ) . '</p>';
                break;
            case 'premium':
                $message .= '<br>' . wp_kses( __( 'You\'re using <strong>premium</strong> plugin plan.', 'super-notes' ), array(
                    'strong' => array(),
                ) ) . '</p>';
                break;
        }
        $cta = '<p><a href="' . esc_url( admin_url( 'admin.php?page=wdpsn-pricing' ) ) . '">' . esc_html( __( 'Upgrate to premium', 'super-notes' ) ) . '</a> ' . esc_html( __( 'to unlock more amazing features and get access to support.', 'super-notes' ) ) . '</p>';
        return $message . ' ' . $cta;
    }
    
    /**
     * Install dummy data with ajax
     */
    public static function install_dummy_data()
    {
        check_ajax_referer( 'install-dummy-data', 'nonce' );
        $check_note_type = ( isset( $_POST['confirmed'] ) && 'true' === $_POST['confirmed'] ? false : true );
        
        if ( true === $check_note_type ) {
            $note_type = WDPSN_DataCache::get_note_type_id();
            
            if ( false !== $note_type ) {
                echo  'confirm_needed' ;
                wp_die();
            }
        
        }
        
        $note_type_id = wp_insert_post( array(
            'post_title'  => esc_html( __( 'Main notes', 'super-notes' ) ),
            'post_status' => 'publish',
            'post_type'   => 'wdpsn_note_types',
            'meta_input'  => array(
            'wdpsn_note_types_owners'   => wp_json_encode( array(
            'can_everyone' => 'yes',
        ) ),
            'wdpsn_note_types_viewers'  => wp_json_encode( array(
            'can_everyone' => 'yes',
        ) ),
            'wdpsn_note_types_location' => wp_json_encode( array(
            'everywhere' => 'yes',
        ) ),
            'wdpsn_note_types_styles'   => wp_json_encode( array(
            'color_scheme' => 'alert',
        ) ),
        ),
        ) );
        
        if ( is_wp_error( $note_type_id ) ) {
            echo  'try_again' ;
            wp_die();
        }
        
        $single_note_id = WDPSN_SingleNotes::add( $note_type_id, array(
            'type' => 'global',
            'id'   => 'global',
        ), array(
            'content' => esc_html( __( 'Hey! I am an example note - you can edit or delete me :)', 'super-notes' ) ),
        ) );
        echo  esc_html( ( false === $single_note_id ? 'try_again' : 'ok' ) ) ;
        wp_die();
    }

}