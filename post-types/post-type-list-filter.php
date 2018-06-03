<?php
class CustomPostTypeListFilter
{
    public function __construct() {
        $post_types = ['post'];
        foreach( $post_types as $post_type ) {
            add_filter( "manage_{$post_type}_posts_columns", [$this, 'columns'] );
            add_filter( "manage_edit-{$post_type}_sortable_columns", [$this, 'sortable_columns'] );
            add_action( "manage_{$post_type}_manage_posts_custom_column", [$this, 'column_content'], 10, 2 );
        }
    }

    public function columns( $columns ) {
        $columns['_custom_column'] = 'Custom Column';
        return $columns;
    }

    public function sortable_columns( $columns ) {
        return $columns;
    }

    public function column_content( $column_name, $post_id ) {
        switch( $column_name ) {
            case '_custom_column':
                // Do something...
                break;
        }
    }
}