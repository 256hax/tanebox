<?php
/**
 * CPTUIEXT_Admin_About Class File.
 *
 * @package CPTUIExtended
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Main initiation class.
 *
 * @internal
 *
 * @since 1.0.0
 */
class CPTUIEXT_Admin_About {

	/**
	 * Whether or not we have a new install.
	 *
	 * @var bool
	 * @since 1.4.0
	 */
	protected $is_new_install = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->is_new_install = isset( $_GET['is_new_install'] );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'cptui_extra_menu_items', array( $this, 'add_options_page' ), 10, 2 );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}


	/**
	 * Register our setting to WP.
	 *
	 * @since 1.0.0
	 */
	public function init() {}

	/**
	 * Register our dashboard pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug parent plugin menu.
	 * @param string $capability access.
	 */
	public function add_options_page( $parent_slug, $capability ) {

		add_dashboard_page(
			__( 'CPTUI Extended', 'cptuiext' ),
			__( 'CPTUI Extended', 'cptuiext' ),
			$capability,
			'cptui_about',
			array( $this, 'about_screen' )
		);

		// Credits.
		add_dashboard_page(
			__( 'CPTUI Credits',  'cptuiext' ),
			__( 'CPTUI Credits',  'cptuiext' ),
			$capability,
			'cptui_credits',
			array( $this, 'credits_screen' )
		);
	}

	/**
	 * Admin head.
	 *
	 * @since 1.0.0
	 */
	public function admin_head() {

		// About and Credits pages.
		remove_submenu_page( 'index.php', 'cptui_about' );
		remove_submenu_page( 'index.php', 'cptui_credits' );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 */
	public function welcome_text() {
		?>
		<h1>
			<?php printf( esc_html__( 'Welcome to CPTUI Extended %s', 'cptuiext' ), cptui_extended()->version ); ?>
		</h1>

		<div class="about-text">
			<?php
			echo ( $this->is_new_install )
				? esc_html__( 'Way to level up with CPTUI Extended, which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', 'cptuiext' )
				: esc_html__( 'Thank you for continuing to level up with the latest version of CPTUI Extended which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', 'cptuiext' );
			?>
		</div>

		<div class="plugin-badge">
			<img src="<?php echo esc_url( cptui_extended()->url . 'assets/images/icon.png' ); ?>" alt="<?php esc_html_e( 'insert custom post type shortcodes.', 'cptuiext' ); ?>">
		</div>

		<?php

	}

	/**
	 * Display our about page.
	 *
	 * @since 1.0.0
	 */
	public function about_screen() {
	?>

		<div class="wrap about-wrap">

			<?php

			$this->welcome_text();
			$this->tab_navigation( __METHOD__ );
			if ( $this->is_new_install ) : ?>

				<div id="welcome-panel" class="welcome-panel">
					<div class="welcome-panel-content">
					</div>
				</div>

			<?php endif; ?>

			<div class="headline-feature">
				<div class="featured-image">
					<img src="<?php echo esc_url( cptui_extended()->url . 'assets/images/new-options.png' ); ?>" alt="<?php esc_html_e( 'insert custom post type shortcodes.', 'cptuiext' ); ?>">
				</div>

				<p class="introduction"><?php esc_html_e( 'Easily display a single post type from any registered custom post type.', 'cptuiext' ); ?>  </p>
				<p><?php esc_html_e( 'CPTUI Extended 1.3.0 adds another new template with one focused on individual posts from your custom post types, whether from CPTUI or elsewhere.', 'cptuiext' ); ?></p>
				<p><?php printf( esc_html__( 'To %s learn how to create shortcodes and templates %s check out this post.', 'cptuiext' ), '<a href="https://pluginize.com/cptui-extended-shorcode-builder/">', '</a>' ); ?></p>

				<div class="clear"></div>
			</div>

			<hr />

			<div class="bp-features-section">

				<div class="feature-section two-col">
					<div>
						<h3 class="feature-title"><?php esc_html_e( 'Category and tag archive support', 'cptuiext' ); ?></h3>
						<p><?php esc_html_e( 'Out of the box, WordPress does not include custom post types in category and tag archives. Adding them required using a filter and some coding knowledge. With CPTUI-Extended, we have made it as easy as checking a checkbox and hitting save.', 'cptuiext' ); ?></p>
					</div>
					<div class="last-feature">
						<h3 class="feature-title"><?php esc_html_e( 'Google AMP support', 'cptuiext' ); ?></h3>
						<p><?php printf( esc_html__( 'We have made it easy for you to add your post types to Google AMP when using the %s plugin from WordPress.org. Simply check the checkbox at the bottom, and you are good to go.', 'cptuiext' ), '<a href="https://wordpress.org/plugins/amp/">AMP</a>' ); ?></p>
					</div>
				</div>

				<div class="feature-section two-col">
					<div>
						<h3 class="feature-title"><?php esc_html_e( 'Template Hooks', 'cptuiext' ); ?></h3>
						<p><?php esc_html_e( 'We wanted to focus on the templates in this release because displaying your post types is the core feature of the plugin. So, we added a ton of templates hooks so you can display custom data.', 'cptuiext' ); ?> <a href="http://codex.pluginize.com/cptuiext/"><?php esc_html_e( 'Read the documentation to find out about all the new template hooks.', 'cptuiext' ); ?></a></p>
					</div>
					<div class="last-feature">
						<h3 class="feature-title"><?php esc_html_e( 'More customization options in templates', 'cptuiext' ); ?></h3>
						<p><?php esc_html_e( 'With version 1.3.0, we have added more filters and hook parameters to the default templates for the ease of customizing the output. Templates that have a featured image can now have the image size parameter changed. On top of that, each action hook in the template\'s loop now has the current post ID passed as an extra parameter.', 'cptuiext' ); ?></p>
					</div>
				</div>
				<div class="feature-section two-col">
					<div>
						<h3 class="feature-title"><?php esc_html_e( 'Bug fixes and misc changes', 'cptuiext' ); ?></h3>
						<p><?php esc_html_e( 'Lastly, we have squashed a number of bugs and issues that were reported by customers. Users who have Windows based hosting for their site should have fewer issues with regards to JavaScript loading in the post editor. Network-wide post types and taxonomies no longer need an extra refresh for them to appear in single site admin ares. More admin notices have been made dismissable, and taxonomy terms should properly use the term slug in shortcode attributes.', 'cptuiext' ); ?></p>
					</div>
					<div class="last-feature">
					</div>
				</div>

			</div>

			<div class="changelog">
			</div>

			<p>
				<?php echo esc_html_x( 'Learn more:', 'About screen, website links', 'cptuiext' ); ?>
				<a href="https://pluginize.com/blog/">
					<?php echo esc_html_x( 'News', 'About screen, link to project blog', 'cptuiext' ); ?>
				</a> &bullet;
				<a href="http://docs.pluginize.com/">
					<?php echo esc_html_x( 'Documentation', 'About screen, link to documentation', 'cptuiext' ); ?>
				</a> &bullet;
				<a href="https://pluginize.com/change-logs/cptui-extended-change-log/">
					<?php echo esc_html_x( 'Change log', 'cptuiext' ); ?>
				</a>
			</p>

			<p>
				<?php echo esc_html_x( 'Twitter:', 'official Twitter accounts:', 'cptuiext' ); ?>
				<a href="https://twitter.com/pluginize/">
					<?php echo esc_html_x( '@Pluginize', '@pluginize twitter account name', 'cptuiext' ); ?>
				</a>
			</p>

		</div>

		<?php
	}

	/**
	 * Output our tab navigation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tab Active tab.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( 'CPTUIEXT_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( cptui_get_admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', 'cptuiext' ); ?>
			</a><a class="nav-tab <?php if ( 'CPTUIEXT_Admin_About::credits_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( cptui_get_admin_url( add_query_arg( array( 'page' => 'cptui_credits' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'Credits', 'cptuiext' ); ?>
			</a>
		</h2>

	<?php
	}

	/**
	 * Display our credit screen.
	 *
	 * @since 1.0.0
	 */
	public function credits_screen() {
	?>

		<div class="wrap about-wrap">

			<?php $this->welcome_text(); ?>

			<?php $this->tab_navigation( __METHOD__ ); ?>

			<p class="about-description">
				<?php esc_html_e( 'Custom Post Types UI is created by a worldwide network of friendly folks like these.', 'cptuiext' ); ?>
			</p>

			<h3 class="wp-people-group">
				<?php esc_html_e( 'Plugin Contributors', 'cptuiext' ); ?>
			</h3>
			<?php
			$contributors = cptuiext_get_contributors();
			?>
			<ul class="wp-people-group " id="wp-people-group-project-contributors">

				<?php
				foreach ( $contributors as $contributor ) {
					printf(
						'<li class="wp-person" id="wp-person-%s"><a class="web" href="%s"><img alt="" class="gravatar" src="%s">%s</a><span class="title">%s</span></li>',
						$contributor['username'],
						$contributor['profile'],
						$contributor['gravatar'],
						$contributor['displayname'],
						$contributor['title']
					);
				}
				?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Return an array of contributors to CPTUI Extended.
 *
 * @since 1.4.0
 *
 * @return array
 */
function cptuiext_get_contributors() {
	return array(
		array(
			'username'    => 'pluginize',
			'profile'     => esc_attr( 'https://profiles.wordpress.org/pluginize/' ),
			'gravatar'    => esc_attr( '//www.gravatar.com/avatar/210484f55c0df074f663b2b6d082e063?s=60' ),
			'displayname' => 'Pluginize',
			'title'       => esc_html__( 'Contributor', 'cptuiext' ),
		),
		array(
			'username'    => 'webdevstudios',
			'profile'     => esc_attr( 'https://profiles.wordpress.org/webdevstudios/' ),
			'gravatar'    => esc_attr( '//www.gravatar.com/avatar/2596fe59ce16cabfe5ddf5c7d734ef8a?s=60' ),
			'displayname' => 'WebDevStudios',
			'title'       => esc_html__( 'Contributor', 'cptuiext' ),
		),
		array(
			'username'    => 'tw2113',
			'profile'     => esc_attr( 'https://profiles.wordpress.org/tw2113/' ),
			'gravatar'    => esc_attr( '//www.gravatar.com/avatar/a5d7c934621fa1c025b83ee79bc62366?s=60' ),
			'displayname' => 'Michael Beckwith',
			'title'       => esc_html__( 'Contributor', 'cptuiext' ),
		),
		array(
			'username'    => 'vegasgeek',
			'profile'     => esc_attr( 'https://profiles.wordpress.org/vegasgeek/' ),
			'gravatar'    => esc_attr( '//www.gravatar.com/avatar/6f3c8b1e3930788f8fc676c9f23769ac?s=60' ),
			'displayname' => 'John Hawkins',
			'title'       => esc_html__( 'Contributor', 'cptuiext' ),
		),
		array(
			'username'    => 'colorful-tones',
			'profile'     => esc_attr( 'https://profiles.wordpress.org/colorful-tones/' ),
			'gravatar'    => esc_attr( '//www.gravatar.com/avatar/e3dd9f1bbd70a30a63d3d5cc6090059e?s=60' ),
			'displayname' => 'Damon Cook',
			'title'       => esc_html__( 'Contributor', 'cptuiext' ),
		),
	);
}
