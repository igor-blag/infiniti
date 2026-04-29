<?php
if ( ! function_exists( 'infiniti_theme_fonts' ) ) {
	function infiniti_theme_fonts() {
		wp_enqueue_style(
			'infiniti-fonts',
			'https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=PT+Mono:wght@400;500;600;700&display=swap',
			array(),
			null
		);
	}
}
add_action( 'enqueue_block_assets', 'infiniti_theme_fonts' );