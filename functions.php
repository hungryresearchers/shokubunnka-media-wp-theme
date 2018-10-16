<?php
/**
 * Intentionally Blank Theme functions
 *
 * @package WordPress
 * @subpackage intentionally-blank
 */

if ( ! function_exists( 'blank_setup' ) ) :
	/**
	 * Sets up theme defaults and registers the various WordPress features that
	 * this theme supports.
	 */
	function blank_setup() {
		load_theme_textdomain( 'intentionally-blank' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );

		// This theme allows users to set a custom background.
		add_theme_support( 'custom-background', apply_filters( 'intentionally_blank_custom_background_args', array(
			'default-color' => 'f5f5f5',
		) ) );

		add_theme_support( 'custom-logo' );
		add_theme_support( 'custom-logo', array(
			'height'      => 256,
			'width'       => 256,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );

		/**
		 * Sets up theme defaults and registers the various WordPress features that
		 * this theme supports.
		 */
		function blank_custom_logo() {
			if ( function_exists( 'the_custom_logo' ) ) {
				the_custom_logo();
			}
		}
	}
endif; // end function_exists blank_setup.
add_action( 'after_setup_theme', 'blank_setup' );

add_action( 'customize_register', function( $wp_customize ) {
	$wp_customize->remove_section( 'static_front_page' );
} );

/**
 * アイキャッチ画像に対応する
 */
function my_after_setup_theme(){
 // アイキャッチ画像を有効にする
 add_theme_support( 'post-thumbnails' ); 
 // アイキャッチ画像サイズを指定する（横：640px 縦：384）
 // 画像サイズをオーバーした場合は切り抜き
 set_post_thumbnail_size( 640, 384, true ); 
}
add_action( 'after_setup_theme', 'my_after_setup_theme' );


function wp_rest_api_article() {
	$params = array(
    'get_callback' => function($data, $field, $request, $type) {
			$raw_writer = get_user_by('id', $data['author']);
			$writer_data = $raw_writer->data;
			$writer = array(
				'name' => $writer_data->display_name,
				'imgUrl' => get_avatar_url($writer_data->ID)
			);
			$tag_ids = $data['tags'];
			$tags = array();
			foreach ($tag_ids as $tag_id) {
				$tag = get_tag($tag_id);
				array_push($tags, $tag->name);
			};
			$content = $data['content']['rendered'];
			$title = $data['title']['rendered'];

			$article_id = $data['id'];

			$post_id = strip_tags(get_the_term_list( $article_id, 'shop_category'));

			$post_customs = array(
				'open',
				'close',
				'holiday',
				'postalCode',
				'address',
				'shop_map',
				'requiredTime',
				'phoneNumber'
			);
			$shop = array();
			foreach ($post_customs as $key) {
				$shop[$key] = get_post_meta($post_id, $key, true);
			};

			$new_post = array(
				'writer' => $writer,
				'tags' => $tags,
				'content' => $content,
				'title' => $title,
				'thumbnail' => get_the_post_thumbnail_url($article_id),
				'releasedDate' => $data['date'],
				'id' => $article_id,
				'shop' => $shop,
				'postId' => $post_id
			);

			return $new_post;
    },
    'update_callback' => null,
    'schema'          => null,
  );
  register_rest_field( 'article', 'post', $params );
}
add_action( 'rest_api_init', 'wp_rest_api_article');

function wp_rest_api_post() {
	$params = array(
    'get_callback' => function($d, $field, $request, $type) {
			// $raw_writer = get_user_by('id', $data['author']);
			// $writer_data = $raw_writer->data;
			// $writer = array(
			// 	'name' => $writer_data->display_name,
			// 	'imgUrl' => get_avatar_url($writer_data->ID)
			// );
			// $tag_ids = $data['tags'];
			// $tags = array();
			// foreach ($tag_ids as $tag_id) {
			// 	$tag = get_tag($tag_id);
			// 	array_push($tags, $tag->name);
			// };
			// $content = $data['content']['rendered'];
			// $title = $data['title']['rendered'];
				
			// $article_id = $data['id'];
			// $new_post = array(
			// 	'writer' => $writer,
			// 	'tags' => $tags,
			// 	'content' => $content,
			// 	'title' => $title,
			// 	'thumbnail' => get_the_post_thumbnail_url($article_id),
			// 	'releasedDate' => $data['date'],
			// 	'id' => $article_id
			// );
			// $post_id = strip_tags(get_the_term_list( , 'shop_category'));
			$data = get_post_custom();
			$raw_writer = get_user_by('id', $data['author']);
			$writer_data = $raw_writer->data;
			$writer = array(
				'name' => $writer_data->display_name,
				'imgUrl' => get_avatar_url($writer_data->ID)
			);
			$tag_ids = $data['tags'];
			$tags = array();
			foreach ($tag_ids as $tag_id) {
				$tag = get_tag($tag_id);
				array_push($tags, $tag->name);
			};
			$content = $data['content']['rendered'];
			$title = $data['title']['rendered'];
				
			$article_id = $data['id'];
			$new_post = array(
				'writer' => $writer,
				'tags' => $tags,
				'content' => $content,
				'title' => $title,
				'thumbnail' => get_the_post_thumbnail_url($article_id),
				'releasedDate' => $data['date'],
				'id' => $article_id,
			);

			return $new_post;
    },
    'update_callback' => null,
    'schema'          => null,
  );
  register_rest_field( 'post', 'post', $params );
}
add_action( 'rest_api_init', 'wp_rest_api_post');

/**
 * Use * for origin
 */

// function my_customize_rest_cors() {
// 	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
// 	add_filter( 'rest_pre_serve_request', function( $value ) {
// 		header( 'Access-Control-Allow-Origin: *' );
// 		header( 'Access-Control-Allow-Methods: GET' );
// 		header( 'Access-Control-Allow-Credentials: true' );
// 		header( 'Access-Control-Expose-Headers: Link', false );
// 		header( 'Access-Control-Allow-Headers: Content-Type' );
// 		header('Content-type: application/json');
// 		return $value;
// 	} );
// }
// add_action( 'rest_api_init', 'my_customize_rest_cors', 15 );
