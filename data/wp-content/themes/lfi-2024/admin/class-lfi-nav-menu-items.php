<?php

final class WP_Nav_Menu_Custom_Fields {

	public static array $field_keys = [

		'_menu_item_svg_icon' => [
			'title' => 'SVG Icon Name',
			'desc' => 'Set svg icon name (not url)',
		],

		'test_number_field' => [
			'title' => 'Number One',
			'type' => 'number',
			'size' => 'thin', // thin | wide
			// 'desc' => 'some text',
		],

		'test_number_field_two' => [
			'title' => 'Number Two',
			'type' => 'number',
			'size' => 'thin', // thin | wide
			// 'desc' => 'some text',
		],

	];

	public static function init(): void {

		if( is_admin() ){
			add_action( 'wp_nav_menu_item_custom_fields', [ __CLASS__, 'add_fileds' ], 10, 2 );
			add_action( 'wp_update_nav_menu_item', [ __CLASS__, 'save_fields' ], 10, 2 );
		}
		// front
		else {
			add_filter( 'walker_nav_menu_start_el', [ __CLASS__, 'nav_menu_start_el' ], 10, 2 );
			add_filter( 'wp_nav_menu_args', [ __CLASS__, 'nav_menu_args' ] );
		}

	}

	public static function add_fileds( $item_id, $item ) {

		foreach( self::$field_keys as $meta_key => $data ){

			$value = get_post_meta( $item_id, $meta_key, true );
			$title = $data['title'];
			$type = $data['type'] ?? 'text';
			$size = $data['size'] ?? 'wide';

			$desc = empty( $data['desc'] ) ? '' : '<span class="description">'. $data['desc'] .'</span>';
			?>
			<p class="field-<?= $meta_key ?> description description-<?= $size ?>">
				<?= $title ?>
				<br/>
				<input class="widefat edit-menu-item-<?= $meta_key ?>"
					   type="<?= $type ?>"
					   name="<?= sprintf( '%s[%s]', $meta_key, $item_id ) ?>"
					   id="menu-item-<?= $item_id ?>"
					   value="<?= esc_attr( $value ) ?>"/>

				<?= $desc ?>
			</p>
			<?php
		}
	}

	public static function save_fields( $menu_id, $item_id ) {

		foreach( self::$field_keys as $meta_key => $data ){
			self::save_field( $menu_id, $item_id, $meta_key );
		}
	}

	private static function save_field( $menu_id, $item_id, $meta_key ) {

		if( ! isset( $_POST[ $meta_key ][ $item_id ] ) ){
			return;
		}

		$val = $_POST[ $meta_key ][ $item_id ];

		if( $val ){
			update_post_meta( $item_id, $meta_key, sanitize_text_field( $val ) );
		}
		else{
			delete_post_meta( $item_id, $meta_key );
		}

	}

	public static function nav_menu_start_el( $item_output, $post  ){

		$svg = $post->_menu_item_svg_icon ?: '';
		if( $svg ){
			$svg = get_svg( $svg );
		}

		return str_replace( '{SVG}', $svg, $item_output );
	}

	public static function nav_menu_args( $args ){

		if( empty( $args['link_before'] ) ){
			$args['link_before'] = '{SVG}';
		}

		return $args;
	}

}