<?php
/**
 * Sensor: Content
 *
 * Content sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress contents (posts, pages and custom posts).
 *
 * 2000 User created a new blog post and saved it as draft
 * 2001 User published a blog post
 * 2002 User modified a published blog post
 * 2008 User permanently deleted a blog post from the trash
 * 2012 User moved a blog post to the trash
 * 2014 User restored a blog post from trash
 * 2016 User changed blog post category
 * 2017 User changed blog post URL
 * 2019 User changed blog post author
 * 2021 User changed blog post status
 * 2023 User created new category
 * 2024 User deleted category
 * 2025 User changed the visibility of a blog post
 * 2027 User changed the date of a blog post
 * 2047 User changed the parent of a page
 * 2048 User changed the template of a page
 * 2049 User set a post as sticky
 * 2050 User removed post from sticky
 * 2052 User changed generic tables
 * 2065 User modified content for a published post
 * 2073 User submitted a post for review
 * 2074 User scheduled a post
 * 2086 User changed title of a post
 * 2100 User opened a post in the editor
 * 2101 User viewed a post
 * 2111 User disabled Comments/Trackbacks and Pingbacks on a published post
 * 2112 User enabled Comments/Trackbacks and Pingbacks on a published post
 * 2119 User added blog post tag
 * 2120 User removed blog post tag
 * 2121 User created new tag
 * 2122 User deleted tag
 * 2123 User renamed tag
 * 2124 User changed tag slug
 * 2125 User changed tag description
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Content extends WSAL_AbstractSensor {

	/**
	 * Old post.
	 *
	 * @var stdClass
	 */
	protected $_old_post = null;

	/**
	 * Old permalink.
	 *
	 * @var string
	 */
	protected $_old_link = null;

	/**
	 * Old categories.
	 *
	 * @var array
	 */
	protected $_old_cats = null;

	/**
	 * Old tags.
	 *
	 * @var array
	 */
	protected $_old_tags = null;

	/**
	 * Old path to file.
	 *
	 * @var string
	 */
	protected $_old_tmpl = null;

	/**
	 * Old post is marked as sticky.
	 *
	 * @var boolean
	 */
	protected $_old_stky = null;

	/**
	 * Old Post Status.
	 *
	 * @var string
	 */
	protected $old_status = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'EventWordPressInit' ) );
		}
		add_action( 'transition_post_status', array( $this, 'EventPostChanged' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'EventPostDeleted' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'EventPostTrashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'EventPostUntrashed' ) );
		add_action( 'edit_category', array( $this, 'EventChangedCategoryParent' ) );
		add_action( 'wp_insert_post', array( $this, 'SetRevisionLink' ), 10, 3 );
		add_action( 'publish_future_post', array( $this, 'EventPublishFuture' ), 10, 1 );
		add_filter( 'post_edit_form_tag', array( $this, 'EditingPost' ), 10, 1 );

		add_action( 'create_category', array( $this, 'EventCategoryCreation' ), 10, 1 );
		add_action( 'create_post_tag', array( $this, 'EventTagCreation' ), 10, 1 );
		add_filter( 'wp_update_term_data', array( $this, 'event_terms_rename' ), 10, 4 );

		// Check if MainWP Child Plugin exists.
		if ( is_plugin_active( 'mainwp-child/mainwp-child.php' ) ) {
			add_action( 'mainwp_before_post_update', array( $this, 'event_mainwp_init' ), 10, 2 );
		}

		add_action( 'admin_action_edit', array( $this, 'edit_post_in_gutenberg' ), 10 );
		add_action( 'pre_post_update', array( $this, 'gutenberg_post_edit_init' ), 10, 2 );
		add_action( 'save_post', array( $this, 'gutenberg_post_changed' ), 10, 3 );
		add_action( 'set_object_terms', array( $this, 'gutenberg_post_terms_changed' ), 10, 4 );
		add_action( 'post_stuck', array( $this, 'gutenberg_post_stuck' ), 10, 1 );
		add_action( 'post_unstuck', array( $this, 'gutenberg_post_unstuck' ), 10, 1 );
	}

	/**
	 * Return Post Event Data.
	 *
	 * @since 3.2.4
	 *
	 * @param WP_Post $post - WP Post object.
	 * @return mixed
	 */
	public function get_post_event_data( $post ) {
		if ( ! empty( $post ) && $post instanceof WP_Post ) {
			$event_data = array(
				'PostID'     => $post->ID,
				'PostType'   => $post->post_type,
				'PostTitle'  => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate'   => $post->post_date,
				'PostUrl'    => get_permalink( $post->ID ),
			);
			return $event_data;
		}
		return false;
	}

	/**
	 * Method: Triggered when terms are renamed.
	 *
	 * @param array  $data     Term data to be updated.
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 * @param array  $args     Arguments passed to wp_update_term().
	 * @since 2.6.9
	 */
	public function event_terms_rename( $data, $term_id, $taxonomy, $args ) {
		// Check if the taxonomy is `post tag`.
		if ( 'post_tag' === $taxonomy ) {
			// Get data.
			$new_name = ( isset( $data['name'] ) ) ? $data['name'] : false;
			$new_slug = ( isset( $data['slug'] ) ) ? $data['slug'] : false;
			$new_desc = ( isset( $args['description'] ) ) ? $args['description'] : false;

			// Get old data.
			$term      = get_term( $term_id, $taxonomy );
			$old_name  = $term->name;
			$old_slug  = $term->slug;
			$old_desc  = $term->description;
			$term_link = $this->get_tag_link( $term_id );

			// Update if both names are not same.
			if ( $old_name !== $new_name ) {
				$this->plugin->alerts->Trigger(
					2123, array(
						'old_name' => $old_name,
						'new_name' => $new_name,
						'TagLink'  => $term_link,
					)
				);
			}

			// Update if both slugs are not same.
			if ( $old_slug !== $new_slug ) {
				$this->plugin->alerts->Trigger(
					2124, array(
						'tag'      => $new_name,
						'old_slug' => $old_slug,
						'new_slug' => $new_slug,
						'TagLink'  => $term_link,
					)
				);
			}

			// Update if both descriptions are not same.
			if ( $old_desc !== $new_desc ) {
				$this->plugin->alerts->Trigger(
					2125, array(
						'tag'        => $new_name,
						'TagLink'    => $term_link,
						'old_desc'   => $old_desc,
						'new_desc'   => $new_desc,
						'ReportText' => $old_desc . '|' . $new_desc,
					)
				);
			}
		} elseif ( 'category' === $taxonomy ) { // Check if the taxonomy is `category`.
			// Get new data.
			$new_name = ( isset( $data['name'] ) ) ? $data['name'] : false;
			$new_slug = ( isset( $data['slug'] ) ) ? $data['slug'] : false;

			// Get old data.
			$term      = get_term( $term_id, $taxonomy );
			$old_name  = $term->name;
			$old_slug  = $term->slug;
			$term_link = $this->get_tag_link( $term_id );

			// Log event if both names are not same.
			if ( $old_name !== $new_name ) {
				$this->plugin->alerts->Trigger(
					2127, array(
						'old_name' => $old_name,
						'new_name' => $new_name,
						'cat_link' => $term_link,
					)
				);
			}

			// Log event if both slugs are not same.
			if ( $old_slug !== $new_slug ) {
				$this->plugin->alerts->Trigger(
					2128, array(
						'CategoryName' => $new_name,
						'old_slug'     => $old_slug,
						'new_slug'     => $new_slug,
						'cat_link'     => $term_link,
					)
				);
			}
		}

		// Return data for the filter.
		return $data;
	}

	/**
	 * Gets the alert code based on the type of post.
	 *
	 * @param stdClass $post - The post.
	 * @param integer  $type_post - Alert code type post.
	 * @param integer  $type_page - Alert code type page.
	 * @param integer  $type_custom - Alert code type custom.
	 * @return integer - Alert code.
	 */
	protected function GetEventTypeForPostType( $post, $type_post, $type_page, $type_custom ) {
		switch ( $post->post_type ) {
			case 'page':
				return $type_page;
			case 'post':
				return $type_post;
			default:
				return $type_custom;
		}
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventWordPressInit() {
		// Load old data, if applicable.
		$this->RetrieveOldData();

		// Check for category changes.
		$this->CheckCategoryDeletion();

		// Check for tag changes.
		$this->check_tag_deletion();
	}

	/**
	 * Retrieve Old data.
	 *
	 * @global mixed $_POST - Post data.
	 */
	protected function RetrieveOldData() {
		// Set filter input args.
		$filter_input_args = array(
			'post_ID'  => FILTER_VALIDATE_INT,
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action'   => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( isset( $post_array['_wpnonce'] )
			&& isset( $post_array['post_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			if ( isset( $post_array ) && isset( $post_array['post_ID'] )
				&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				&& ! ( isset( $post_array['action'] ) && 'autosave' == $post_array['action'] )
			) {
				$post_id         = intval( $post_array['post_ID'] );
				$this->_old_post = get_post( $post_id );
				$this->_old_link = get_permalink( $post_id );
				$this->_old_tmpl = $this->GetPostTemplate( $this->_old_post );
				$this->_old_cats = $this->GetPostCategories( $this->_old_post );
				$this->_old_tags = $this->get_post_tags( $this->_old_post );
				$this->_old_stky = in_array( $post_id, get_option( 'sticky_posts' ) );
			}
		} elseif ( isset( $post_array['post_ID'] ) && current_user_can( 'edit_post', $post_array['post_ID'] ) ) {
			$post_id         = intval( $post_array['post_ID'] );
			$this->_old_post = get_post( $post_id );
			$this->_old_link = get_permalink( $post_id );
			$this->_old_tmpl = $this->GetPostTemplate( $this->_old_post );
			$this->_old_cats = $this->GetPostCategories( $this->_old_post );
			$this->_old_tags = $this->get_post_tags( $this->_old_post );
			$this->_old_stky = in_array( $post_id, get_option( 'sticky_posts' ) );
		}
	}

	/**
	 * Method: Collect old post data before MainWP Post update event.
	 *
	 * @param array $new_post - Array of new post data.
	 * @param array $post_custom - Array of data related to MainWP.
	 */
	public function event_mainwp_init( $new_post, $post_custom ) {
		// Get post id.
		$post_id = isset( $post_custom['_mainwp_edit_post_id'][0] ) ? $post_custom['_mainwp_edit_post_id'][0] : false;

		// Check if ID exists.
		if ( $post_id ) {
			// Get post.
			$post = get_post( $post_id );

			// If post exists.
			if ( ! empty( $post ) ) {
				$this->_old_post = $post;
				$this->_old_link = get_permalink( $post_id );
				$this->_old_tmpl = $this->GetPostTemplate( $this->_old_post );
				$this->_old_cats = $this->GetPostCategories( $this->_old_post );
				$this->_old_tags = $this->get_post_tags( $this->_old_post );
				$this->_old_stky = in_array( $post_id, get_option( 'sticky_posts' ) );
			}
		}
	}

	/**
	 * Method: Collect old post data before post update event in gutenberg.
	 *
	 * @since 3.2.4
	 *
	 * @param int   $post_id   - Post ID.
	 * @param array $post_data - Array of post data.
	 */
	public function gutenberg_post_edit_init( $post_id, $post_data ) {
		// Check if rest api request constant is set.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			// Get post.
			$post = get_post( $post_id );

			// If post exists.
			if ( ! empty( $post ) && $post instanceof WP_Post ) {
				$this->_old_post  = $post;
				$this->_old_link  = get_permalink( $post_id );
				$this->_old_tmpl  = $this->GetPostTemplate( $this->_old_post );
				$this->_old_cats  = $this->GetPostCategories( $this->_old_post );
				$this->_old_tags  = $this->get_post_tags( $this->_old_post );
				$this->_old_stky  = in_array( $post_id, get_option( 'sticky_posts' ) );
				$this->old_status = $post->post_status;
			}
		}
	}

	/**
	 * Get the template path.
	 *
	 * @param WP_Post $post - The post.
	 * @return string - Full path to file.
	 */
	protected function GetPostTemplate( $post ) {
		$id       = $post->ID;
		$template = get_page_template_slug( $id );
		$pagename = $post->post_name;

		$templates = array();
		if ( $template && 0 === validate_file( $template ) ) {
			$templates[] = $template;
		}
		if ( $pagename ) {
			$templates[] = "page-$pagename.php";
		}
		if ( $id ) {
			$templates[] = "page-$id.php";
		}
		$templates[] = 'page.php';

		return get_query_template( 'page', $templates );
	}

	/**
	 * Get post categories (array of category names).
	 *
	 * @param stdClass $post - The post.
	 * @return array - List of categories.
	 */
	protected function GetPostCategories( $post ) {
		return wp_get_post_categories(
			$post->ID, array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Get post tags (array of tag names).
	 *
	 * @param stdClass $post - The post.
	 * @return array - List of tags.
	 */
	protected function get_post_tags( $post ) {
		return wp_get_post_tags(
			$post->ID, array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Check all the post changes.
	 *
	 * @param string   $new_status - New status.
	 * @param string   $old_status - Old status.
	 * @param stdClass $post - The post.
	 */
	public function EventPostChanged( $new_status, $old_status, $post ) {
		// Ignorable states.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( empty( $post->post_type ) ) {
			return;
		}
		if ( 'revision' == $post->post_type ) {
			return;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Check if Yoast SEO is active.
		$is_yoast = is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' );
		if ( $is_yoast && ! isset( $_POST['classic-editor'] ) ) {
			return;
		}

		// Set filter input args.
		$filter_input_args = array(
			'post_ID'              => FILTER_VALIDATE_INT,
			'_wpnonce'             => FILTER_SANITIZE_STRING,
			'original_post_status' => FILTER_SANITIZE_STRING,
			'sticky'               => FILTER_SANITIZE_STRING,
			'action'               => FILTER_SANITIZE_STRING,
			'_inline_edit'         => FILTER_SANITIZE_STRING,
			'mainwpsignature'      => FILTER_SANITIZE_STRING,
			'function'             => FILTER_SANITIZE_STRING,
			'new_post'             => FILTER_SANITIZE_STRING,
			'post_custom'          => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// Check MainWP $_POST members.
		$new_post    = isset( $post_array['new_post'] ) ? $post_array['new_post'] : false;
		$post_custom = isset( $post_array['post_custom'] ) ? $post_array['post_custom'] : false;
		$post_custom = maybe_unserialize( base64_decode( $post_custom ) );

		// Verify nonce.
		if (
			isset( $post_array['_wpnonce'] )
			&& isset( $post_array['post_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			// Edit Post Screen.
			$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
			$this->trigger_post_change_alerts( $old_status, $new_status, $post, $original, isset( $post_array['sticky'] ) );
		} elseif (
			isset( $post_array['_inline_edit'] )
			&& 'inline-save' === $post_array['action']
			&& wp_verify_nonce( $post_array['_inline_edit'], 'inlineeditnonce' )
		) {
			// Quick Post Edit.
			$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
			$this->trigger_post_change_alerts( $old_status, $new_status, $post, $original, isset( $post_array['sticky'] ) );
		} elseif (
			isset( $post_array['mainwpsignature'] )
			&& ! empty( $new_post )
			&& ! empty( $post_custom )
		) {
			// Check sticky post.
			$sticky = false;
			if ( isset( $post_custom['_sticky'] ) && is_array( $post_custom['_sticky'] ) ) {
				foreach ( $post_custom['_sticky'] as $key => $meta_value ) {
					if ( 'sticky' === base64_decode( $meta_value ) ) {
						$sticky = true;
					}
				}
			}
			$this->trigger_post_change_alerts( $old_status, $new_status, $post, false, $sticky, 'mainwp' );
		} elseif (
			isset( $post_array['mainwpsignature'] )
			&& isset( $post_array['function'] )
			&& 'post_action' === $post_array['function']
			&& isset( $post_array['action'] )
			&& ( 'unpublish' === $post_array['action'] || 'publish' === $post_array['action'] )
		) {
			$this->check_mainwp_status_change( $post, $old_status, $new_status );
		} else {
			// Ignore nav menu post type or post revision.
			if ( 'nav_menu_item' === get_post_type( $post->ID ) || wp_is_post_revision( $post->ID ) ) {
				return;
			}

			$event = 0;
			if ( 'auto-draft' === $old_status && ( 'auto-draft' !== $new_status && 'inherit' !== $new_status ) ) {
				// Post published.
				$event = 2001;
			} elseif ( 'auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status ) ) {
				// Ignore it.
			} elseif ( 'trash' === $new_status ) {
				// Post deleted.
			} elseif ( 'trash' === $old_status ) {
				// Post restored.
			} else {
				// Post edited.
				$event = 2002;
			}

			if ( $event ) {
				// Get event data.
				$editor_link = $this->GetEditorLink( $post ); // Editor link.
				$event_data  = $this->get_post_event_data( $post ); // Post event data.

				// Set editor link in the event data.
				$event_data[ $editor_link['name'] ] = $editor_link['value'];

				// Log the event.
				$this->plugin->alerts->Trigger( $event, $event_data );
			}
		}
	}

	/**
	 * Method: Trigger Post Change Alerts.
	 *
	 * @param string   $old_status - Old status.
	 * @param string   $new_status - New status.
	 * @param stdClass $post       - The post.
	 * @param string   $original   - Original Post Status.
	 * @param string   $sticky     - Sticky post.
	 * @param string   $dashboard  - Dashboard from which the change is coming from.
	 * @since 1.0.0
	 */
	public function trigger_post_change_alerts( $old_status, $new_status, $post, $original, $sticky, $dashboard = false ) {
		WSAL_Sensors_Request::SetVars(
			array(
				'$new_status' => $new_status,
				'$old_status' => $old_status,
				'$original'   => $original,
			)
		);
		// Run checks.
		if ( $this->_old_post && ! $dashboard ) { // Change is coming from WP Dashboard.
			if ( $this->CheckOtherSensors( $this->_old_post ) ) {
				return;
			}
			if ( 'auto-draft' == $old_status || 'auto-draft' == $original ) {
				// Handle create post events.
				$this->CheckPostCreation( $this->_old_post, $post );
			} else {
				// Handle update post events.
				$changes = 0;
				$changes = $this->CheckAuthorChange( $this->_old_post, $post )
					+ $this->CheckStatusChange( $this->_old_post, $post )
					+ $this->CheckParentChange( $this->_old_post, $post )
					+ $this->CheckStickyChange( $this->_old_stky, $sticky, $post )
					+ $this->CheckVisibilityChange( $this->_old_post, $post, $old_status, $new_status )
					+ $this->CheckTemplateChange( $this->_old_tmpl, $this->GetPostTemplate( $post ), $post )
					+ $this->CheckCategoriesChange( $this->_old_cats, $this->GetPostCategories( $post ), $post )
					+ $this->check_tags_change( $this->_old_tags, $this->get_post_tags( $post ), $post )
					+ $this->CheckDateChange( $this->_old_post, $post )
					+ $this->CheckPermalinkChange( $this->_old_link, get_permalink( $post->ID ), $post )
					+ $this->CheckCommentsPings( $this->_old_post, $post );

				$this->CheckModificationChange( $post->ID, $this->_old_post, $post, $changes );
			}
		} elseif ( ! $this->_old_post && 'mainwp' === $dashboard ) {
			if ( $this->CheckOtherSensors( $this->_old_post ) ) {
				return;
			}
			if ( 'auto-draft' === $old_status ) {
				// Handle create post events.
				$this->CheckPostCreation( $this->_old_post, $post );
			}
		} elseif ( $this->_old_post && 'mainwp' === $dashboard ) {
			if ( 'auto-draft' === $old_status ) {
				// Get MainWP $_POST members.
				// @codingStandardsIgnoreStart
				$new_post = isset( $_POST['new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['new_post'] ) ) : false;
				// @codingStandardsIgnoreEnd

				// Get WordPress Post.
				$new_post   = maybe_unserialize( base64_decode( $new_post ) );
				$post_catgs = filter_input( INPUT_POST, 'post_category', FILTER_SANITIZE_STRING );

				// Post categories.
				$post_categories = rawurldecode( isset( $post_catgs ) ? base64_decode( $post_catgs ) : null );
				$post_categories = explode( ',', $post_categories );

				// Post tags.
				$post_tags = rawurldecode( isset( $new_post['post_tags'] ) ? $new_post['post_tags'] : null );
				$post_tags = sanitize_text_field( $post_tags ); // Sanitize the string of tags.
				$post_tags = str_replace( ' ', '', $post_tags );
				$post_tags = explode( ',', $post_tags );

				// Handle update post events.
				$changes = 0;
				$changes = $this->CheckAuthorChange( $this->_old_post, $post )
					+ $this->CheckStatusChange( $this->_old_post, $post )
					+ $this->CheckParentChange( $this->_old_post, $post )
					+ $this->CheckStickyChange( $this->_old_stky, $sticky, $post )
					+ $this->CheckVisibilityChange( $this->_old_post, $post, $old_status, $new_status )
					+ $this->CheckTemplateChange( $this->_old_tmpl, $this->GetPostTemplate( $post ), $post )
					+ $this->CheckCategoriesChange( $this->_old_cats, $post_categories, $post )
					+ $this->check_tags_change( $this->_old_tags, $post_tags, $post )
					+ $this->CheckDateChange( $this->_old_post, $post )
					+ $this->CheckPermalinkChange( $this->_old_link, get_permalink( $post->ID ), $post )
					+ $this->CheckCommentsPings( $this->_old_post, $post );

				$this->CheckModificationChange( $post->ID, $this->_old_post, $post, $changes );
			}
		}
	}

	/**
	 * Check all the post changes.
	 *
	 * @since 3.2.4
	 *
	 * @param integer $post_id - Post ID.
	 * @param WP_Post $post    - WP Post object.
	 * @param boolean $update  - True if post update, false if post is new.
	 */
	public function gutenberg_post_changed( $post_id, $post, $update ) {
		// Ignorable states.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				// Check post creation event.
				if ( 'auto-draft' === $this->_old_post->post_status && 'draft' === $post->post_status ) {
					$this->CheckPostCreation( $this->_old_post, $post, true );
				}
			}
			return;
		}

		if ( empty( $post->post_type ) || 'revision' === $post->post_type || 'trash' === $post->post_status ) {
			return;
		}

		if ( $update && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			if ( 'draft' === $this->_old_post->post_status && 'publish' === $post->post_status ) {
				$this->CheckPostCreation( $this->_old_post, $post, true );
			} else {
				// Handle update post events.
				$changes = 0;
				$changes = $this->CheckAuthorChange( $this->_old_post, $post )
					+ $this->CheckStatusChange( $this->_old_post, $post )
					+ $this->CheckParentChange( $this->_old_post, $post )
					+ $this->CheckVisibilityChange( $this->_old_post, $post, $this->old_status, $post->post_status )
					+ $this->CheckTemplateChange( $this->_old_tmpl, $this->GetPostTemplate( $post ), $post )
					+ $this->CheckDateChange( $this->_old_post, $post )
					+ $this->CheckPermalinkChange( $this->_old_link, get_permalink( $post->ID ), $post )
					+ $this->CheckCommentsPings( $this->_old_post, $post );
				$this->CheckModificationChange( $post->ID, $this->_old_post, $post, $changes );
			}
		}
	}

	/**
	 * Check post creation.
	 *
	 * @global array $_POST
	 *
	 * @param WP_Post $old_post     - Old post.
	 * @param WP_Post $new_post     - New post.
	 * @param boolean $is_gutenberg - Gutenberg flag.
	 */
	protected function CheckPostCreation( $old_post, $new_post, $is_gutenberg = false ) {
		// Set filter input args.
		$filter_input_args = array(
			'action' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// Check if the post is coming from MainWP.
		$mainwp = filter_input( INPUT_POST, 'mainwpsignature', FILTER_SANITIZE_STRING );

		if ( ! empty( $mainwp ) ) {
			$post_array['action'] = 'editpost';
		}

		/**
		 * Nonce is already verified at this point.
		 *
		 * @see $this->EventPostChanged();
		 */
		$wp_actions = array( 'editpost', 'heartbeat' );
		if ( isset( $post_array['action'] ) && in_array( $post_array['action'], $wp_actions ) && ! $is_gutenberg ) {
			if ( ! in_array( $new_post->post_type, array( 'attachment', 'revision', 'nav_menu_item' ) ) ) {
				$this->log_post_creation_event( $new_post );
			}
		} elseif ( $is_gutenberg ) {
			$this->log_post_creation_event( $new_post );
		}
	}

	/**
	 * Log Post Creation Event.
	 *
	 * @since 3.2.4
	 *
	 * @param WP_Post $new_post – New WP post object.
	 */
	private function log_post_creation_event( $new_post ) {
		if ( ! empty( $new_post ) && $new_post instanceof WP_Post ) {
			$event        = 0;
			$is_scheduled = false;
			switch ( $new_post->post_status ) {
				case 'publish':
					$event = 2001;
					break;
				case 'draft':
					$event = 2000;
					break;
				case 'future':
					$event        = 2074;
					$is_scheduled = true;
					break;
				case 'pending':
					$event = 2073;
					break;
			}
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $new_post ); // Editor link.
				$event_data  = $this->get_post_event_data( $new_post ); // Post event data.

				// Set editor link in the event data.
				$event_data[ $editor_link['name'] ] = $editor_link['value'];

				if ( $is_scheduled ) {
					$event_data['PublishingDate'] = $new_post->post_date;
					$this->plugin->alerts->Trigger( $event, $event_data );
				} else {
					$this->plugin->alerts->Trigger( $event, $event_data );
				}
			}
		}
	}

	/**
	 * Post future publishing.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPublishFuture( $post_id ) {
		$post = get_post( $post_id );
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2001, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post permanently deleted.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostDeleted( $post_id ) {
		// Set filter input args.
		$filter_input_args = array(
			'action'   => FILTER_SANITIZE_STRING,
			'_wpnonce' => FILTER_SANITIZE_STRING,
		);

		// Filter $_GET array for security.
		$get_array = filter_input_array( INPUT_GET, $filter_input_args );

		// Exclude CPTs from external plugins.
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}

		// Get MainWP $_POST members.
		$filter_post_args = array(
			'id'              => FILTER_VALIDATE_INT,
			'action'          => FILTER_SANITIZE_STRING,
			'mainwpsignature' => FILTER_SANITIZE_STRING,
		);
		$post_array       = filter_input_array( INPUT_POST, $filter_post_args );

		// Verify nonce.
		if ( isset( $get_array['_wpnonce'] ) && wp_verify_nonce( $get_array['_wpnonce'], 'delete-post_' . $post_id ) ) {
			$wp_actions = array( 'delete' );
			if ( isset( $get_array['action'] ) && in_array( $get_array['action'], $wp_actions, true ) ) {
				if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item' ), true ) ) { // Ignore attachments, revisions and menu items.
					$event = 2008;
					// Check WordPress backend operations.
					if ( $this->CheckAutoDraft( $event, $post->post_title ) ) {
						return;
					}

					$event_data = $this->get_post_event_data( $post ); // Get event data.
					$this->plugin->alerts->Trigger( $event, $event_data ); // Log event.
				}
			}
		} elseif (
			isset( $post_array['mainwpsignature'] )
			&& isset( $post_array['action'] )
			&& 'delete' === $post_array['action']
			&& ! empty( $post_array['id'] )
		) {
			if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item' ), true ) ) { // Ignore attachments, revisions and menu items.
				// Check WordPress backend operations.
				if ( $this->CheckAutoDraft( 2008, $post->post_title ) ) {
					return;
				}

				$event_data = $this->get_post_event_data( $post ); // Get event data.
				$this->plugin->alerts->Trigger( 2008, $event_data ); // Log event.
			}
		}
	}

	/**
	 * Post moved to the trash.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostTrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2012, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post restored from trash.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostUntrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2014, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post date changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckDateChange( $oldpost, $newpost ) {
		$from = strtotime( $oldpost->post_date );
		$to   = strtotime( $newpost->post_date );

		if ( 'draft' == $oldpost->post_status ) {
			return 0;
		}

		if ( $from != $to ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2027, array(
					'PostID'             => $oldpost->ID,
					'PostType'           => $oldpost->post_type,
					'PostTitle'          => $oldpost->post_title,
					'PostStatus'         => $oldpost->post_status,
					'PostDate'           => $newpost->post_date,
					'PostUrl'            => get_permalink( $oldpost->ID ),
					'OldDate'            => $oldpost->post_date,
					'NewDate'            => $newpost->post_date,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Categories changed.
	 *
	 * @param array    $old_cats - Old categories.
	 * @param array    $new_cats - New categories.
	 * @param stdClass $post - The post.
	 */
	protected function CheckCategoriesChange( $old_cats, $new_cats, $post ) {
		$old_cats = implode( ', ', (array) $old_cats );
		$new_cats = implode( ', ', (array) $new_cats );

		if ( $old_cats !== $new_cats ) {
			$event = $this->GetEventTypeForPostType( $post, 2016, 0, 2016 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $post );
				$alert_data  = array(
					'PostID'             => $post->ID,
					'PostType'           => $post->post_type,
					'PostTitle'          => $post->post_title,
					'PostStatus'         => $post->post_status,
					'PostDate'           => $post->post_date,
					'PostUrl'            => get_permalink( $post->ID ),
					'OldCategories'      => $old_cats ? $old_cats : 'no categories',
					'NewCategories'      => $new_cats ? $new_cats : 'no categories',
					$editor_link['name'] => $editor_link['value'],
				);
				$this->plugin->alerts->Trigger( $event, $alert_data );
				return 1;
			}
		}
	}

	/**
	 * Method: This function make sures that alert 2016
	 * has not been triggered before triggering categories
	 * & tags events.
	 *
	 * @since 3.2.4
	 *
	 * @param WSAL_AlertManager $manager - WSAL Alert Manager.
	 * @return bool
	 */
	public function must_not_contain_events( WSAL_AlertManager $manager ) {
		if ( $manager->WillOrHasTriggered( 2016 ) ) {
			return false;
		} elseif ( $manager->WillOrHasTriggered( 2119 ) ) {
			return false;
		} elseif ( $manager->WillOrHasTriggered( 2120 ) ) {
			return false;
		} elseif ( $manager->WillOrHasTriggered( 2049 ) ) {
			return false;
		} elseif ( $manager->WillOrHasTriggered( 2050 ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Check if post terms changed via Gutenberg.
	 *
	 * @since 3.2.4
	 *
	 * @param int    $post_id  - Post ID.
	 * @param array  $terms    - Array of terms.
	 * @param array  $tt_ids   - Array of taxonomy term ids.
	 * @param string $taxonomy - Taxonomy slug.
	 */
	public function gutenberg_post_terms_changed( $post_id, $terms, $tt_ids, $taxonomy ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$post = get_post( $post_id );

			if ( is_wp_error( $post ) ) {
				return;
			}

			if ( 'auto-draft' === $post->post_status ) {
				return;
			}

			if ( 'post_tag' === $taxonomy ) {
				// Check tags change event.
				$this->check_tags_change( $this->_old_tags, $this->get_post_tags( $post ), $post );
			} else {
				// Check categories change event.
				$this->CheckCategoriesChange( $this->_old_cats, $this->GetPostCategories( $post ), $post );
			}
		}
	}

	/**
	 * Post Stuck Event.
	 *
	 * @since 3.2.4
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function gutenberg_post_stuck( $post_id ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$this->log_sticky_post_event( $post_id, 2049 );
		}
	}

	/**
	 * Post Unstuck Event.
	 *
	 * @since 3.2.4
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function gutenberg_post_unstuck( $post_id ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$this->log_sticky_post_event( $post_id, 2050 );
		}
	}

	/**
	 * Log post stuck/unstuck events.
	 *
	 * @since 3.2.4
	 *
	 * @param integer $post_id - Post ID.
	 * @param integer $event   - Event ID.
	 */
	private function log_sticky_post_event( $post_id, $event ) {
		// Get post.
		$post = get_post( $post_id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		$editor_link = $this->GetEditorLink( $post ); // Editor link.
		$event_data  = $this->get_post_event_data( $post ); // Event data.

		$event_data[ $editor_link['name'] ] = $editor_link['value'];
		$this->plugin->alerts->Trigger( $event, $event_data );
	}

	/**
	 * Tags changed.
	 *
	 * @param array    $old_tags - Old tags.
	 * @param array    $new_tags - New tags.
	 * @param stdClass $post - The post.
	 */
	protected function check_tags_change( $old_tags, $new_tags, $post ) {
		// Check for added tags.
		$added_tags = array_diff( $new_tags, $old_tags );

		// Check for removed tags.
		$removed_tags = array_diff( $old_tags, $new_tags );

		// Convert tags arrays to string.
		$old_tags     = implode( ', ', (array) $old_tags );
		$new_tags     = implode( ', ', (array) $new_tags );
		$added_tags   = implode( ', ', (array) $added_tags );
		$removed_tags = implode( ', ', (array) $removed_tags );

		// Declare event variables.
		$add_event    = '';
		$remove_event = '';
		if ( $old_tags !== $new_tags && ! empty( $added_tags ) ) {
			$add_event   = 2119;
			$editor_link = $this->GetEditorLink( $post );
			$post_status = ( 'publish' === $post->post_status ) ? 'published' : $post->post_status;
			$this->plugin->alerts->Trigger(
				$add_event, array(
					'PostID'             => $post->ID,
					'PostType'           => $post->post_type,
					'PostStatus'         => $post_status,
					'PostTitle'          => $post->post_title,
					'PostDate'           => $post->post_date,
					'PostUrl'            => get_permalink( $post->ID ),
					'tag'                => $added_tags ? $added_tags : 'no tags',
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}

		if ( $old_tags !== $new_tags && ! empty( $removed_tags ) ) {
			$remove_event = 2120;
			$editor_link  = $this->GetEditorLink( $post );
			$post_status  = ( 'publish' === $post->post_status ) ? 'published' : $post->post_status;
			$this->plugin->alerts->Trigger(
				$remove_event, array(
					'PostID'             => $post->ID,
					'PostType'           => $post->post_type,
					'PostStatus'         => $post_status,
					'PostTitle'          => $post->post_title,
					'PostDate'           => $post->post_date,
					'PostUrl'            => get_permalink( $post->ID ),
					'tag'                => $removed_tags ? $removed_tags : 'no tags',
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}

		if ( $add_event || $remove_event ) {
			return 1;
		}
	}

	/**
	 * Author changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckAuthorChange( $oldpost, $newpost ) {
		if ( $oldpost->post_author != $newpost->post_author ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$old_author  = get_userdata( $oldpost->post_author );
			$old_author  = ( is_object( $old_author ) ) ? $old_author->user_login : 'N/A';
			$new_author  = get_userdata( $newpost->post_author );
			$new_author  = ( is_object( $new_author ) ) ? $new_author->user_login : 'N/A';
			$this->plugin->alerts->Trigger(
				2019, array(
					'PostID'             => $oldpost->ID,
					'PostType'           => $oldpost->post_type,
					'PostTitle'          => $oldpost->post_title,
					'PostStatus'         => $oldpost->post_status,
					'PostDate'           => $oldpost->post_date,
					'PostUrl'            => get_permalink( $oldpost->ID ),
					'OldAuthor'          => $old_author,
					'NewAuthor'          => $new_author,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Status changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckStatusChange( $oldpost, $newpost ) {
		// Set filter input args.
		$filter_input_args = array(
			'publish' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		/**
		 * Nonce is already verified at this point.
		 *
		 * @see $this->EventPostChanged();
		 */
		if ( $oldpost->post_status != $newpost->post_status ) {
			if ( isset( $post_array['publish'] ) ) {
				// Special case (publishing a post).
				$editor_link = $this->GetEditorLink( $newpost );
				$this->plugin->alerts->Trigger(
					2001, array(
						'PostID'             => $newpost->ID,
						'PostType'           => $newpost->post_type,
						'PostTitle'          => $newpost->post_title,
						'PostStatus'         => $newpost->post_status,
						'PostDate'           => $newpost->post_date,
						'PostUrl'            => get_permalink( $newpost->ID ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
			} else {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					2021, array(
						'PostID'             => $oldpost->ID,
						'PostType'           => $oldpost->post_type,
						'PostTitle'          => $oldpost->post_title,
						'PostStatus'         => $newpost->post_status,
						'PostDate'           => $oldpost->post_date,
						'PostUrl'            => get_permalink( $oldpost->ID ),
						'OldStatus'          => $oldpost->post_status,
						'NewStatus'          => $newpost->post_status,
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
			return 1;
		}
	}

	/**
	 * Post parent changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckParentChange( $oldpost, $newpost ) {
		if ( $oldpost->post_parent != $newpost->post_parent ) {
			$event = $this->GetEventTypeForPostType( $oldpost, 0, 2047, 0 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID' => $oldpost->ID,
						'PostType' => $oldpost->post_type,
						'PostTitle' => $oldpost->post_title,
						'PostStatus' => $oldpost->post_status,
						'PostDate' => $oldpost->post_date,
						'OldParent' => $oldpost->post_parent,
						'NewParent' => $newpost->post_parent,
						'OldParentName' => $oldpost->post_parent ? get_the_title( $oldpost->post_parent ) : 'no parent',
						'NewParentName' => $newpost->post_parent ? get_the_title( $newpost->post_parent ) : 'no parent',
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
	}

	/**
	 * Permalink changed.
	 *
	 * @param string   $old_link - Old permalink.
	 * @param string   $new_link - New permalink.
	 * @param stdClass $post - The post.
	 */
	protected function CheckPermalinkChange( $old_link, $new_link, $post ) {
		if ( $old_link !== $new_link ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				2017, array(
					'PostID' => $post->ID,
					'PostType' => $post->post_type,
					'PostTitle' => $post->post_title,
					'PostStatus' => $post->post_status,
					'PostDate' => $post->post_date,
					'OldUrl' => $old_link,
					'NewUrl' => $new_link,
					$editor_link['name'] => $editor_link['value'],
					'ReportText' => '"' . $old_link . '"|"' . $new_link . '"',
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Post visibility changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 * @param string   $old_status - Old status.
	 * @param string   $new_status - New status.
	 */
	protected function CheckVisibilityChange( $oldpost, $newpost, $old_status, $new_status ) {
		if ( 'draft' == $old_status || 'draft' == $new_status ) {
			return;
		}

		$old_visibility = '';
		$new_visibility = '';

		if ( $oldpost->post_password ) {
			$old_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} elseif ( 'publish' == $old_status ) {
			$old_visibility = __( 'Public', 'wp-security-audit-log' );
		} elseif ( 'private' == $old_status ) {
			$old_visibility = __( 'Private', 'wp-security-audit-log' );
		}

		if ( $newpost->post_password ) {
			$new_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} elseif ( 'publish' == $new_status ) {
			$new_visibility = __( 'Public', 'wp-security-audit-log' );
		} elseif ( 'private' == $new_status ) {
			$new_visibility = __( 'Private', 'wp-security-audit-log' );
		}

		if ( $old_visibility && $new_visibility && ($old_visibility != $new_visibility) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2025, array(
					'PostID' => $oldpost->ID,
					'PostType' => $oldpost->post_type,
					'PostTitle' => $oldpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $oldpost->post_date,
					'PostUrl' => get_permalink( $oldpost->ID ),
					'OldVisibility' => $old_visibility,
					'NewVisibility' => $new_visibility,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Post template changed.
	 *
	 * @param string   $old_tmpl - Old template path.
	 * @param string   $new_tmpl - New template path.
	 * @param stdClass $post - The post.
	 */
	protected function CheckTemplateChange( $old_tmpl, $new_tmpl, $post ) {
		if ( $old_tmpl != $new_tmpl ) {
			$event = $this->GetEventTypeForPostType( $post, 0, 2048, 0 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID' => $post->ID,
						'PostType' => $post->post_type,
						'PostTitle' => $post->post_title,
						'PostStatus' => $post->post_status,
						'PostDate' => $post->post_date,
						'OldTemplate' => ucwords( str_replace( array( '-', '_' ), ' ', basename( $old_tmpl, '.php' ) ) ),
						'NewTemplate' => ucwords( str_replace( array( '-', '_' ), ' ', basename( $new_tmpl, '.php' ) ) ),
						'OldTemplatePath' => $old_tmpl,
						'NewTemplatePath' => $new_tmpl,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
	}

	/**
	 * Post sets as sticky changes.
	 *
	 * @param string  $old_stky - Old template path.
	 * @param string  $new_stky - New template path.
	 * @param WP_Post $post     - The post.
	 */
	protected function CheckStickyChange( $old_stky, $new_stky, $post ) {
		if ( $old_stky != $new_stky ) {
			$event       = $new_stky ? 2049 : 2050;
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				$event, array(
					'PostID'             => $post->ID,
					'PostType'           => $post->post_type,
					'PostTitle'          => $post->post_title,
					'PostStatus'         => $post->post_status,
					'PostDate'           => $post->post_date,
					'PostUrl'            => get_permalink( $post->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Post modified content.
	 *
	 * @param integer  $post_id  – Post ID.
	 * @param stdClass $oldpost  – Old post.
	 * @param stdClass $newpost  – New post.
	 * @param int      $modified – Set to 0 if no changes done to the post.
	 */
	public function CheckModificationChange( $post_id, $oldpost, $newpost, $modified ) {
		if ( $this->CheckOtherSensors( $oldpost ) ) {
			return;
		}
		$changes = $this->CheckTitleChange( $oldpost, $newpost );
		if ( ! $changes ) {
			$content_changed = $oldpost->post_content != $newpost->post_content; // TODO what about excerpts?

			if ( $oldpost->post_modified != $newpost->post_modified ) {
				$event = 0;

				// Check if content changed.
				if ( $content_changed ) {
					$event = 2065;
				} elseif ( ! $modified ) {
					$event = 2002;
				}
				if ( $event ) {
					if ( 2002 === $event ) {
						// Get Yoast alerts.
						$yoast_alerts = $this->plugin->alerts->get_alerts_by_sub_category( 'Yoast SEO' );

						// Check all alerts.
						foreach ( $yoast_alerts as $alert_code => $alert ) {
							if ( $this->plugin->alerts->WillOrHasTriggered( $alert_code ) ) {
								return 0; // Return if any Yoast alert has or will trigger.
							}
						}

						// Get post meta events.
						$meta_events = array( 2053, 2054, 2055, 2062 );
						foreach ( $meta_events as $meta_event ) {
							if ( $this->plugin->alerts->WillOrHasTriggered( $meta_event ) ) {
								return 0; // Return if any meta event has or will trigger.
							}
						}
					}

					$editor_link = $this->GetEditorLink( $oldpost );
					$event_data  = array(
						'PostID'             => $post_id,
						'PostType'           => $oldpost->post_type,
						'PostTitle'          => $oldpost->post_title,
						'PostStatus'         => $oldpost->post_status,
						'PostDate'           => $oldpost->post_date,
						'PostUrl'            => get_permalink( $post_id ),
						$editor_link['name'] => $editor_link['value'],
					);

					if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
						$event_data['RevisionLink'] = $this->get_post_revision( $post_id, $oldpost );

						if ( 2002 === $event ) {
							$this->plugin->alerts->TriggerIf( $event, $event_data, array( $this, 'must_not_contain_events' ) );
						} else {
							$this->plugin->alerts->Trigger( $event, $event_data );
						}
					} else {
						$this->plugin->alerts->Trigger( $event, $event_data );
					}
					return 1;
				}
			}
		}
	}

	/**
	 * New category created.
	 *
	 * @param integer $category_id - Category ID.
	 */
	public function EventCategoryCreation( $category_id ) {
		$category      = get_category( $category_id );
		$category_link = $this->getCategoryLink( $category_id );
		$this->plugin->alerts->Trigger(
			2023, array(
				'CategoryName' => $category->name,
				'Slug'         => $category->slug,
				'CategoryLink' => $category_link,
			)
		);
	}

	/**
	 * New tag created.
	 *
	 * @param int $tag_id - Tag ID.
	 */
	public function EventTagCreation( $tag_id ) {
		$tag      = get_tag( $tag_id );
		$tag_link = $this->get_tag_link( $tag_id );
		$this->plugin->alerts->Trigger(
			2121, array(
				'TagName' => $tag->name,
				'Slug'    => $tag->slug,
				'TagLink' => $tag_link,
			)
		);
	}

	/**
	 * Category deleted.
	 *
	 * @global array $_POST - Post data.
	 */
	protected function CheckCategoryDeletion() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'action2' => FILTER_SANITIZE_STRING,
			'taxonomy' => FILTER_SANITIZE_STRING,
			'delete_tags' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_REQUIRE_ARRAY,
			),
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( empty( $post_array ) ) {
			return;
		}
		$action = ! empty( $post_array['action'] ) ? $post_array['action']
			: ( ! empty( $post_array['action2'] ) ? $post_array['action2'] : '');
		if ( ! $action ) {
			return;
		}

		$category_ids = array();

		if ( isset( $post_array['taxonomy'] ) ) {
			if ( 'delete' == $action
				&& 'category' == $post_array['taxonomy']
				&& ! empty( $post_array['delete_tags'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'bulk-tags' ) ) {
				// Bulk delete.
				foreach ( $post_array['delete_tags'] as $delete_tag ) {
					$category_ids[] = $delete_tag;
				}
			} elseif ( 'delete-tag' == $action
				&& 'category' == $post_array['taxonomy']
				&& ! empty( $post_array['tag_ID'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'delete-tag_' . $post_array['tag_ID'] ) ) {
				// Single delete.
				$category_ids[] = $post_array['tag_ID'];
			}
		}

		foreach ( $category_ids as $category_id ) {
			$category = get_category( $category_id );
			$category_link = $this->getCategoryLink( $category_id );
			$this->plugin->alerts->Trigger(
				2024, array(
					'CategoryID' => $category_id,
					'CategoryName' => $category->cat_name,
					'Slug' => $category->slug,
					'CategoryLink' => $category_link,
				)
			);
		}
	}

	/**
	 * Tag deleted.
	 *
	 * @global array $_POST - Post data
	 */
	protected function check_tag_deletion() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'action2' => FILTER_SANITIZE_STRING,
			'taxonomy' => FILTER_SANITIZE_STRING,
			'delete_tags' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_REQUIRE_ARRAY,
			),
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// If post array is empty then return.
		if ( empty( $post_array ) ) {
			return;
		}

		// Check for action.
		$action = ! empty( $post_array['action'] ) ? $post_array['action']
			: ( ! empty( $post_array['action2'] ) ? $post_array['action2'] : '' );
		if ( ! $action ) {
			return;
		}

		$tag_ids = array();

		if ( isset( $post_array['taxonomy'] ) ) {
			if ( 'delete' === $action
				&& 'post_tag' === $post_array['taxonomy']
				&& ! empty( $post_array['delete_tags'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'bulk-tags' ) ) {
				// Bulk delete.
				foreach ( $post_array['delete_tags'] as $delete_tag ) {
					$tag_ids[] = $delete_tag;
				}
			} elseif ( 'delete-tag' === $action
				&& 'post_tag' === $post_array['taxonomy']
				&& ! empty( $post_array['tag_ID'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'delete-tag_' . $post_array['tag_ID'] ) ) {
				// Single delete.
				$tag_ids[] = $post_array['tag_ID'];
			}
		}

		foreach ( $tag_ids as $tag_id ) {
			$tag = get_tag( $tag_id );
			$this->plugin->alerts->Trigger(
				2122, array(
					'TagID' => $tag_id,
					'TagName' => $tag->name,
					'Slug' => $tag->slug,
				)
			);
		}
	}

	/**
	 * Changed the parent of the category.
	 *
	 * @global array $_POST - Post data.
	 */
	public function EventChangedCategoryParent() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'name' => FILTER_SANITIZE_STRING,
			'parent' => FILTER_SANITIZE_STRING,
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( empty( $post_array ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( isset( $post_array['_wpnonce'] )
			&& isset( $post_array['name'] )
			&& isset( $post_array['tag_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-tag_' . $post_array['tag_ID'] ) ) {
			$category = get_category( $post_array['tag_ID'] );
			$category_link = $this->getCategoryLink( $post_array['tag_ID'] );
			if ( 0 != $category->parent ) {
				$old_parent = get_category( $category->parent );
				$old_parent_name = (empty( $old_parent )) ? 'no parent' : $old_parent->name;
			} else {
				$old_parent_name = 'no parent';
			}
			if ( isset( $post_array['parent'] ) ) {
				$new_parent = get_category( $post_array['parent'] );
				$new_parent_name = (empty( $new_parent )) ? 'no parent' : $new_parent->name;
			}

			if ( $old_parent_name !== $new_parent_name ) {
				$this->plugin->alerts->Trigger(
					2052, array(
						'CategoryName' => $category->name,
						'OldParent' => $old_parent_name,
						'NewParent' => $new_parent_name,
						'CategoryLink' => $category_link,
					)
				);
			}
		}
	}

	/**
	 * Check auto draft and the setting: Hide Plugin in Plugins Page
	 *
	 * @param integer $code  - Alert code.
	 * @param string  $title - Title.
	 * @return boolean
	 */
	private function CheckAutoDraft( $code, $title ) {
		if ( 2008 == $code && 'auto-draft' == $title ) {
			// To do: Check setting else return false.
			if ( 1 == $this->plugin->settings->IsWPBackend() ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Builds revision link.
	 *
	 * @param integer $revision_id - Revision ID.
	 * @return string|null - Link.
	 */
	private function getRevisionLink( $revision_id ) {
		if ( ! empty( $revision_id ) ) {
			return admin_url( 'revision.php?revision=' . $revision_id );
		} else {
			return null;
		}
	}

	/**
	 * Return post revision link.
	 *
	 * @param integer $post_id - Post ID.
	 * @param WP_Post $post    - WP Post object.
	 * @return string
	 */
	private function get_post_revision( $post_id, $post ) {
		$revisions = wp_get_post_revisions( $post_id );
		if ( ! empty( $revisions ) ) {
			$revision = array_shift( $revisions );
			return $this->getRevisionLink( $revision->ID );
		}
	}

	/**
	 * Builds category link.
	 *
	 * @param integer $category_id - Category ID.
	 * @return string|null - Link.
	 */
	private function getCategoryLink( $category_id ) {
		if ( ! empty( $category_id ) ) {
			return admin_url( 'term.php?taxnomy=category&tag_ID=' . $category_id );
		} else {
			return null;
		}
	}

	/**
	 * Builds tag link.
	 *
	 * @param integer $tag_id - Tag ID.
	 * @return string|null - Link.
	 */
	private function get_tag_link( $tag_id ) {
		if ( ! empty( $tag_id ) ) {
			return admin_url( 'term.php?taxnomy=post_tag&tag_ID=' . $tag_id );
		} else {
			return null;
		}
	}

	/**
	 * Ignore post from BBPress, WooCommerce Plugin
	 * Triggered on the Sensors
	 *
	 * @param WP_Post $post - The post.
	 */
	private function CheckOtherSensors( $post ) {
		if ( empty( $post ) || ! isset( $post->post_type ) ) {
			return false;
		}
		switch ( $post->post_type ) {
			case 'forum':
			case 'topic':
			case 'reply':
			case 'product':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Triggered after save post for add revision link.
	 *
	 * @param integer  $post_id - Post ID.
	 * @param stdClass $post    - Post.
	 * @param bool     $update  - True if update.
	 */
	public function SetRevisionLink( $post_id, $post, $update ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		$revisions = wp_get_post_revisions( $post_id );
		if ( ! empty( $revisions ) ) {
			$revision = array_shift( $revisions );

			$obj_occ = new WSAL_Models_Occurrence();
			$occ     = $obj_occ->GetByPostID( $post_id );
			$occ     = count( $occ ) ? $occ[0] : null;
			if ( ! empty( $occ ) ) {
				$revision_link = $this->getRevisionLink( $revision->ID );
				if ( ! empty( $revision_link ) ) {
					$occ->SetMetaValue( 'RevisionLink', $revision_link );
				}
			}
		}
	}

	/**
	 * Alerts for Editing of Posts, Pages and Custom Post Types.
	 *
	 * @param WP_Post $post - Post.
	 */
	public function EditingPost( $post ) {
		if ( is_user_logged_in() && is_admin() ) {
			// Log event.
			$this->post_opened_in_editor( $post );
		}
		return $post;
	}

	/**
	 * Alert for Editing of Posts and Custom Post Types in Gutenberg.
	 *
	 * @since 3.2.4
	 */
	public function edit_post_in_gutenberg() {
		global $pagenow;

		if ( 'post.php' !== $pagenow ) {
			return;
		}

		// @codingStandardsIgnoreStart
		$post_id = isset( $_GET['post'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['post'] ) ) : false;
		// @codingStandardsIgnoreEnd

		// Check post id.
		if ( empty( $post_id ) ) {
			return;
		}

		if ( is_user_logged_in() && is_admin() ) {
			// Get post.
			$post = get_post( $post_id );

			// Log event.
			$this->post_opened_in_editor( $post );
		}
	}

	/**
	 * Post Opened for Editing in WP Editors.
	 *
	 * @param WP_Post $post – Post object.
	 */
	public function post_opened_in_editor( $post ) {
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		// Check ignored post types.
		if ( $this->CheckOtherSensors( $post ) ) {
			return $post;
		}

		$current_path = isset( $_SERVER['SCRIPT_NAME'] ) ? esc_url_raw( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) . '?post=' . $post->ID : false;
		$referrer     = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : false;

		if ( ! empty( $referrer )
			&& strpos( $referrer, $current_path ) !== false ) {
			// Ignore this if we were on the same page so we avoid double audit entries.
			return $post;
		}
		if ( ! empty( $post->post_title ) ) {
			$event = 2100;
			if ( ! $this->WasTriggered( $event ) ) {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID'             => $post->ID,
						'PostType'           => $post->post_type,
						'PostTitle'          => $post->post_title,
						'PostStatus'         => $post->post_status,
						'PostDate'           => $post->post_date,
						'PostUrl'            => get_permalink( $post->ID ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
		}
	}

	/**
	 * Check if the alert was triggered.
	 *
	 * @param integer $alert_id - Alert code.
	 * @return boolean
	 */
	private function WasTriggered( $alert_id ) {
		$query = new WSAL_Models_OccurrenceQuery();
		$query->addOrderBy( 'created_on', true );
		$query->setLimit( 1 );
		$last_occurence = $query->getAdapter()->Execute( $query );
		if ( ! empty( $last_occurence ) ) {
			if ( $last_occurence[0]->alert_id == $alert_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Changed title of a post.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	private function CheckTitleChange( $oldpost, $newpost ) {
		if ( $oldpost->post_title != $newpost->post_title ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2086, array(
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostUrl' => get_permalink( $newpost->ID ),
					'OldTitle' => $oldpost->post_title,
					'NewTitle' => $newpost->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Comments/Trackbacks and Pingbacks check.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	private function CheckCommentsPings( $oldpost, $newpost ) {
		$result = 0;
		$editor_link = $this->GetEditorLink( $newpost );

		// Comments.
		if ( $oldpost->comment_status != $newpost->comment_status ) {
			$type = 'Comments';

			if ( 'open' == $newpost->comment_status ) {
				$event = $this->GetCommentsPingsEvent( $newpost, 'enable' );
			} else {
				$event = $this->GetCommentsPingsEvent( $newpost, 'disable' );
			}

			$this->plugin->alerts->Trigger(
				$event, array(
					'Type' => $type,
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostUrl' => get_permalink( $newpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			$result = 1;
		}
		// Trackbacks and Pingbacks.
		if ( $oldpost->ping_status != $newpost->ping_status ) {
			$type = 'Trackbacks and Pingbacks';

			if ( 'open' == $newpost->ping_status ) {
				$event = $this->GetCommentsPingsEvent( $newpost, 'enable' );
			} else {
				$event = $this->GetCommentsPingsEvent( $newpost, 'disable' );
			}

			$this->plugin->alerts->Trigger(
				$event, array(
					'Type' => $type,
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostUrl' => get_permalink( $newpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			$result = 1;
		}
		return $result;
	}

	/**
	 * Comments/Trackbacks and Pingbacks event code.
	 *
	 * @param stdClass $post - The post.
	 * @param string   $status - The status.
	 */
	private function GetCommentsPingsEvent( $post, $status ) {
		if ( 'disable' == $status ) {
			$event = 2111;
		} else {
			$event = 2112;
		}
		return $event;
	}

	/**
	 * Method: Check status change of a post from MainWP Dashboard.
	 *
	 * @param WP_Post $post       - WP_Post object.
	 * @param string  $old_status - Old post status.
	 * @param string  $new_status - New post status.
	 * @since 3.2.2
	 */
	private function check_mainwp_status_change( $post, $old_status, $new_status ) {
		// Verify function arguments.
		if ( empty( $post ) || ! $post instanceof WP_Post || empty( $old_status ) || empty( $new_status ) ) {
			return;
		}

		// Check to see if old & new statuses don't match.
		if ( $old_status !== $new_status ) {
			if ( 'publish' === $new_status ) {
				// Special case (publishing a post).
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					2001, array(
						'PostID'             => $post->ID,
						'PostType'           => $post->post_type,
						'PostTitle'          => $post->post_title,
						'PostStatus'         => $post->post_status,
						'PostDate'           => $post->post_date,
						'PostUrl'            => get_permalink( $post->ID ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
			} else {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					2021, array(
						'PostID'             => $post->ID,
						'PostType'           => $post->post_type,
						'PostTitle'          => $post->post_title,
						'PostStatus'         => $post->post_status,
						'PostDate'           => $post->post_date,
						'PostUrl'            => get_permalink( $post->ID ),
						'OldStatus'          => $old_status,
						'NewStatus'          => $new_status,
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post - The post.
	 * @return array $editor_link - Name and value link.
	 */
	private function GetEditorLink( $post ) {
		$name = 'EditorLinkPost';
		// $name .= ( 'page' == $post->post_type ) ? 'Page' : 'Post' ;
		$value       = get_edit_post_link( $post->ID );
		$editor_link = array(
			'name'  => $name,
			'value' => $value,
		);
		return $editor_link;
	}
}
