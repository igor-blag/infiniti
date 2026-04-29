<?php
/**
 * Demo content creation logic.
 *
 * Called from bin/setup-demo.php (WP-CLI) or via AJAX from admin notice.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function infiniti_find_or_create_page( $title, $content, $slug ) {
	$existing = get_page_by_title( $title, OBJECT, 'page' );
	if ( $existing ) {
		return $existing->ID;
	}

	return wp_insert_post( [
		'post_title'   => $title,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_name'    => $slug,
	] );
}

function infiniti_import_image( $file_path, $title ) {
	$existing = get_posts( [
		'post_type'   => 'attachment',
		'title'       => $title,
		'numberposts' => 1,
	] );
	if ( ! empty( $existing ) ) {
		return $existing[0]->ID;
	}

	$upload_dir = wp_upload_dir();
	$filename   = wp_unique_filename( $upload_dir['path'], basename( $file_path ) );
	$dest       = $upload_dir['path'] . '/' . $filename;

	copy( $file_path, $dest );

	$filetype  = wp_check_filetype( $filename );
	$attach_id = wp_insert_attachment( [
		'guid'           => $upload_dir['url'] . '/' . $filename,
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	], $dest );

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$meta = wp_generate_attachment_metadata( $attach_id, $dest );
	wp_update_attachment_metadata( $attach_id, $meta );

	return $attach_id;
}

function infiniti_find_or_create_category( $name, $description = '' ) {
	$term = get_term_by( 'name', $name, 'category' );
	if ( $term ) {
		return $term->term_id;
	}
	$result = wp_insert_term( $name, 'category', [ 'description' => $description ] );
	return is_wp_error( $result ) ? 0 : $result['term_id'];
}

function infiniti_install_demo_content( $_log = null ) {
	$theme_dir = get_template_directory();

	if ( is_null( $_log ) ) {
		$_log = function ( $msg ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::log( $msg );
			} else {
				error_log( 'Infiniti Demo: ' . $msg );
			}
		};
	}

	$_log( '=== ИнфинИТи Demo Content Setup ===' );

	// ========== Pages ==========

	$_log( '--- Pages ---' );

	$pages = [
		[
			'title'   => 'Главная',
			'slug'    => 'home',
			'content' => implode( "\n", [
				'<!-- wp:pattern {"slug":"infiniti/hero"} /-->',
				'<!-- wp:pattern {"slug":"infiniti/programs-cards"} /-->',
				'<!-- wp:pattern {"slug":"infiniti/advantages"} /-->',
				'<!-- wp:pattern {"slug":"infiniti/cta"} /-->',
				'<!-- wp:pattern {"slug":"infiniti/how-it-works"} /-->',
			] ),
		],
		[
			'title'   => 'О центре',
			'slug'    => 'about',
			'content' => '<!-- wp:pattern {"slug":"infiniti/page-about"} /-->',
		],
		[
			'title'   => 'Программы',
			'slug'    => 'programs',
			'content' => '<!-- wp:pattern {"slug":"infiniti/programs-cards"} /-->' . "\n"
			           . '<!-- wp:pattern {"slug":"infiniti/cta"} /-->',
		],
		[
			'title'   => 'Достижения',
			'slug'    => 'achievements',
			'content' => '<!-- wp:pattern {"slug":"infiniti/page-achievements"} /-->',
		],
		[
			'title'   => 'Профориентация',
			'slug'    => 'career',
			'content' => '<!-- wp:pattern {"slug":"infiniti/page-career"} /-->',
		],
		[
			'title'   => 'Педагогам',
			'slug'    => 'teachers',
			'content' => '<!-- wp:pattern {"slug":"infiniti/page-teachers"} /-->',
		],
		[
			'title'   => 'Контакты',
			'slug'    => 'contacts',
			'content' => '<!-- wp:pattern {"slug":"infiniti/page-contacts"} /-->',
		],
		[
			'title'   => 'Новости',
			'slug'    => 'news',
			'content' => '',
		],
	];

	$page_ids = [];
	foreach ( $pages as $p ) {
		$id = infiniti_find_or_create_page( $p['title'], $p['content'], $p['slug'] );
		$page_ids[ $p['slug'] ] = $id;
		$_log( "  {$p['title']} → ID {$id}" );
	}

	// ========== Reading Settings ==========

	$_log( '--- Reading Settings ---' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	update_option( 'page_for_posts', $page_ids['news'] );

	$_log( '  Front page: Главная (ID ' . $page_ids['home'] . ')' );
	$_log( '  Posts page: Новости (ID ' . $page_ids['news'] . ')' );

	// ========== Categories ==========

	$_log( '--- Categories ---' );

	$categories = [
		'Новости'     => 'Общие новости центра',
		'Мероприятия' => 'Мероприятия и события',
		'Проекты'     => 'Проекты обучающихся',
	];

	$cat_ids = [];
	foreach ( $categories as $name => $desc ) {
		$cat_ids[ $name ] = infiniti_find_or_create_category( $name, $desc );
		$_log( "  {$name} → ID {$cat_ids[$name]}" );
	}

	// ========== Import Images ==========

	$_log( '--- Import Images ---' );

	$news_images = [
		'news-3d.webp'          => '3D-моделирование',
		'news-chess.webp'       => 'Шахматы',
		'news-robotics.webp'    => 'Робототехника',
		'news-competition.webp' => 'Соревнования',
		'news-programming.webp' => 'Программирование',
	];

	$image_ids = [];
	foreach ( $news_images as $file => $title ) {
		$path = $theme_dir . '/assets/' . $file;
		if ( file_exists( $path ) ) {
			$image_ids[ $file ] = infiniti_import_image( $path, $title );
			$_log( "  {$title} → ID {$image_ids[$file]}" );
		} else {
			$_log( "  SKIP: {$file} not found" );
		}
	}

	// ========== Sample Posts ==========

	$_log( '--- Posts ---' );

	$posts_data = [
		[
			'title'   => 'Новые курсы по 3D-моделированию',
			'excerpt' => 'Центр «ИнфинИТи» запускает обновлённую программу по 3D-моделированию.',
			'content' => '<!-- wp:paragraph --><p>Центр «ИнфинИТи» запускает обновлённую программу по 3D-моделированию. Обучающиеся освоят профессиональный 3D-принтер, 3D-сканер и создание цифровых моделей для печати.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Программа рассчитана на учащихся 11–17 лет и включает работу с реальными проектами: от идеи до готового изделия.</p><!-- /wp:paragraph -->',
			'image'   => 'news-3d.webp',
			'cat'     => 'Новости',
		],
		[
			'title'   => 'Команда шахматистов приняла участие в турнире',
			'excerpt' => 'Ученики направления «Шахматы и логика» приняли участие в городском турнире.',
			'content' => '<!-- wp:paragraph --><p>Ученики направления «Шахматы и логика» приняли участие в городском турнире. Развитие стратегического мышления — один из ключевых навыков, который мы формируем на занятиях.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Поздравляем команду с успешным выступлением и желаем дальнейших побед!</p><!-- /wp:paragraph -->',
			'image'   => 'news-chess.webp',
			'cat'     => 'Мероприятия',
		],
		[
			'title'   => 'Робототехники представили проект «Умный гриндер»',
			'excerpt' => 'Обучающиеся представили проект автоматизированного заточного устройства.',
			'content' => '<!-- wp:paragraph --><p>Обучающиеся направления робототехники представили проект автоматизированного заточного устройства на школьной научной конференции.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Проект был высоко оценён комиссией и рекомендован для участия в региональном этапе.</p><!-- /wp:paragraph -->',
			'image'   => 'news-robotics.webp',
			'cat'     => 'Проекты',
		],
		[
			'title'   => 'Участие в чемпионате по пилотированию дронов',
			'excerpt' => 'Ученики центра приняли участие в региональном чемпионате по пилотированию БПЛА.',
			'content' => '<!-- wp:paragraph --><p>Ученики центра приняли участие в региональном чемпионате по пилотированию БПЛА. Наши пилоты вошли в топ участников региона.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Соревнования прошли в три этапа: скоростной облет трассы, точное приземление и автономная миссия.</p><!-- /wp:paragraph -->',
			'image'   => 'news-competition.webp',
			'cat'     => 'Мероприятия',
		],
		[
			'title'   => 'Итоги учебного года: курс программирования на Python',
			'excerpt' => 'Завершился учебный год курса программирования.',
			'content' => '<!-- wp:paragraph --><p>Завершился учебный год курса программирования. Ученики освоили основы Python, создали собственные проекты и защитили их перед комиссией.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Лучшие работы будут представлены на Фестивале компьютерных работ в следующем учебном году.</p><!-- /wp:paragraph -->',
			'image'   => 'news-programming.webp',
			'cat'     => 'Новости',
		],
	];

	foreach ( $posts_data as $post ) {
		$existing = get_page_by_title( $post['title'], OBJECT, 'post' );
		if ( $existing ) {
			$_log( "  EXISTS: {$post['title']} (ID {$existing->ID})" );
			continue;
		}

		$post_id = wp_insert_post( [
			'post_title'   => $post['title'],
			'post_excerpt' => $post['excerpt'],
			'post_content' => $post['content'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_name'    => sanitize_title( $post['title'] ),
			'post_date'    => gmdate( 'Y-m-d H:i:s', time() - rand( 0, 30 * DAY_IN_SECONDS ) ),
		] );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( ! empty( $post['cat'] ) && ! empty( $cat_ids[ $post['cat'] ] ) ) {
				wp_set_post_categories( $post_id, [ $cat_ids[ $post['cat'] ] ] );
			}
			if ( ! empty( $post['image'] ) && ! empty( $image_ids[ $post['image'] ] ) ) {
				set_post_thumbnail( $post_id, $image_ids[ $post['image'] ] );
			}
			$_log( "  {$post['title']} → ID {$post_id}" );
		}
	}

	$_log( '=== Demo content setup complete! ===' );

	return true;
}
