<?php
/**
 * Admin notice with "Install Demo Content" button after theme activation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_admin() ) {
	add_action( 'admin_notices', function () {
		if ( ! get_option( 'infiniti_demo_notice_visible' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$installed = get_option( 'infiniti_demo_installed' );
		if ( $installed ) {
			return;
		}
		?>
	<div class="notice notice-info is-dismissible infiniti-demo-notice">
		<p style="font-size:14px;font-weight:600;margin-bottom:8px;"><?php esc_html_e( 'Добро пожаловать в тему ИнфинИТи!', 'infiniti' ); ?></p>
		<p style="margin-bottom:12px;"><?php esc_html_e( 'Вы можете установить демо-контент, чтобы увидеть тему в действии — страницы, записи и навигация будут созданы автоматически.', 'infiniti' ); ?></p>
		<p>
			<button type="button" class="button button-primary infiniti-install-demo-btn"><?php esc_html_e( 'Установить демо-контент', 'infiniti' ); ?></button>
			<span class="spinner infiniti-demo-spinner" style="float:none;margin-top:4px;"></span>
		</p>
	</div>
	<style>
		.infiniti-demo-spinner.is-active { visibility: visible; }
		.infiniti-demo-result { margin-top: 8px; font-size: 13px; }
		.infiniti-demo-result.success { color: #00a32a; }
		.infiniti-demo-result.error { color: #d63638; }
	</style>
	<script>
	jQuery(document).ready(function($) {
		var $btn = $('.infiniti-install-demo-btn');
		var $spinner = $('.infiniti-demo-spinner');
		var $notice = $('.infiniti-demo-notice');

		$btn.on('click', function() {
			$btn.prop('disabled', true);
			$spinner.addClass('is-active');
			$notice.find('.infiniti-demo-result').remove();

			$.post(ajaxurl, {
				action: 'infiniti_install_demo',
				nonce: '<?php echo esc_js( wp_create_nonce( 'infiniti_install_demo' ) ); ?>'
			}, function(res) {
				$spinner.removeClass('is-active');
				if (res.success) {
					$btn.after('<p class="infiniti-demo-result success">' + res.data + '</p>');
					$btn.text('<?php esc_html_e( 'Демо-контент установлен', 'infiniti' ); ?>').prop('disabled', true);
				} else {
					$btn.after('<p class="infiniti-demo-result error">' + res.data + '</p>');
					$btn.prop('disabled', false);
				}
			}).fail(function() {
				$spinner.removeClass('is-active');
				$btn.after('<p class="infiniti-demo-result error"><?php esc_html_e( 'Ошибка запроса.', 'infiniti' ); ?></p>');
				$btn.prop('disabled', false);
			});
		});

		$notice.on('click', '.notice-dismiss', function() {
			$.post(ajaxurl, {
				action: 'infiniti_dismiss_demo_notice',
				nonce: '<?php echo esc_js( wp_create_nonce( 'infiniti_dismiss_demo' ) ); ?>'
			});
		});
	});
	</script>
	<?php
	} );
}

add_action( 'wp_ajax_infiniti_install_demo', function () {
	check_ajax_referer( 'infiniti_install_demo', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Нет прав.', 'infiniti' ) );
	}

	require_once get_template_directory() . '/includes/demo-content.php';

	$log = function ( $msg ) {};
	$result = infiniti_install_demo_content( $log );

	if ( $result ) {
		update_option( 'infiniti_demo_installed', true );
		wp_send_json_success( __( 'Демо-контент успешно установлен! Страницы, записи и категории созданы.', 'infiniti' ) );
	} else {
		wp_send_json_error( __( 'Ошибка при установке демо-контента.', 'infiniti' ) );
	}
} );

add_action( 'wp_ajax_infiniti_dismiss_demo_notice', function () {
	check_ajax_referer( 'infiniti_dismiss_demo', 'nonce' );
	delete_option( 'infiniti_demo_notice_visible' );
	wp_send_json_success();
} );

add_action( 'after_switch_theme', function () {
	update_option( 'infiniti_demo_notice_visible', true );
	delete_option( 'infiniti_demo_installed' );
} );
