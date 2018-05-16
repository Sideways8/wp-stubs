<?php

class MetaBox
{
    const ID = 'my-metabox';
    const TITLE = 'My Metabox';

    public function __construct() {

        // Define which post types we want to hook.
        $post_types = ['post'];

        foreach( $post_types as $post_type ) {
            add_action( 'add_meta_boxes_' . $post_type, [$this, 'register_meta_box'] );
            add_action( 'save_post_' . $post_type, [$this, 'save_post'] );
        }
    }

    public function register_meta_box() {
        add_meta_box( static::ID, static::TITLE, [$this, 'render_meta_box'] );
    }

    public function render_meta_box( \WP_Post $post ) {
        $some_option = get_post_meta( $post->ID, '_some_option', true );
        ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="_some_option">
                        Some Option
                    </label>
                </th>
                <td>
                    <input type="widefat" value="<?= esc_attr( $some_option ); ?>" id="_some_option" name="_some_option">
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function save( $post_id ) {
        $keys = [
            '_some_options'
        ];

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
                update_post_meta( $post_id, $key, $value );
            }
        }
    }
}