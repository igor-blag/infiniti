<?php
require_once __DIR__ . '/styles.php';
require_once __DIR__ . '/fonts.php';
require_once __DIR__ . '/theme-assets-rewrite.php';
require_once __DIR__ . '/includes/admin-demo-notice.php';

if ( ! function_exists( 'infiniti_theme_setup' ) ) {
	function infiniti_theme_setup() {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		load_theme_textdomain( 'infiniti', get_template_directory() . '/languages' );
	}
}
add_action( 'after_setup_theme', 'infiniti_theme_setup' );

add_action( 'wp_footer', function () {
	?>
	<script>
	document.addEventListener('DOMContentLoaded',function(){var e=document.querySelector('.infiniti-current-year');e&&(e.textContent=new Date().getFullYear())});
	</script>
	<?php
} );

add_action( 'init', function () {
	register_block_style( 'core/group', array(
		'name'         => 'accent-corner',
		'label'        => __( 'Accent Corner', 'infiniti' ),
		'style_handle' => 'infiniti-style',
	) );

	register_block_pattern( 'infiniti/cta-mini', array(
		'title'       => __( 'Mini Call to Action', 'infiniti' ),
		'description' => __( 'A compact call-to-action section with heading, text and button.', 'infiniti' ),
		'categories'  => array( 'call-to-action' ),
		'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"color":{"background":"#e63946"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#e63946;padding:var(--wp--preset--spacing--50) var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"2rem"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:2rem">' . esc_html__( 'Ready to get started?', 'infiniti' ) . '</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->',
	) );
} );