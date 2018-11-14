<?php

/**
 * Allows you to overwrite CATEGORY and TAG link structures with hierarchical support up to two depths.
 *
 * For example:
 *
 * Depth of 1
 * /category/news                            ->  /news
 * /category/news/{post_name}                ->  /news/{post_name}
 *
 * Depth of 2
 * /category/resources/downloads             ->  /resources/downloads
 * /category/resources/downloads/{post_name} ->  /resources/downloads/{post_name}
 *
 * Class Category_Specific_URL_Rewrites
 */
class Category_Specific_URL_Rewrites
{
    /**
     * @var string 'category' or 'post_tag'
     */
    static $taxonomy = 'category';

    /**
     * @var array A map of (parent) category slugs to their taxonomy term ID and post depth.
     */
    public $map = [

        'news' => [
            'id' => 6,
            'depth' => 1,
        ],

        'resources' => [
            'id' => 9,
            'depth' => 2
        ]
    ];

    public function __construct() {
        add_action( 'init', [$this, 'add_category_rewrites'] );
        add_filter( 'category_link', [$this, 'filter_category_link'], 10, 2 );
        add_filter( 'post_link', [$this, 'filter_post_link'], 10, 2 );
        // add_action( 'init', 'flush_rewrite_rules' );
    }

    public function add_category_rewrites() {

        // Default identifier to category.
        $term_identifier = 'category_name';
        if ( 'category' !== static::$taxonomy ) {
            $term_identifier = 'tag';
        }

        foreach( $this->map as $slug => $data ) {
            // Post level depth.
            $depth = $data['depth'];

            // ie: /news/{post_name}
            if ( 1 == $depth ) {
                add_rewrite_rule( '^' . $slug . '/([^/]*)?', 'index.php?name=$matches[1]', 'top' );
                add_rewrite_rule( '^' . $slug . '?', 'index.php?' . $term_identifier . '=' . $slug, 'top' );
            }

            // ie: /resources/{subcategory}/{post_name}
            if ( 2 == $depth ) {
                add_rewrite_rule( '^' . $slug . '/([^/]*)/([^/]*)?', 'index.php?name=$matches[2]', 'top' );
                add_rewrite_rule( '^' . $slug . '/([^/]*)?', 'index.php?' . $term_identifier . '=$matches[1]', 'top' );
                add_rewrite_rule( '^' . $slug . '?', 'index.php?' . $term_identifier . '=' . $slug, 'top' );
            }
        }
    }

    public function filter_category_link( $link, $term_id ) {

        // Get term IDs from map.
        $ids = array_map( function ( $data ) {
            return $data['id'];
        }, $this->map );

        // If the current term exists in our map, return the slug.
        if ( in_array( $term_id, $ids ) ) {
            foreach ( $this->map as $slug => $data ) {
                $id = $data['id'];
                if ( $term_id == $id ) {
                    return home_url() . '/' . $slug;
                }
            }
        }

        // If this term has no parents, return $link.
        $term = static::get_category( $term_id );
        if ( 0 == $term->parent ) {
            return $link;
        }

        // Match this term's parent against our map.
        if ( in_array( $term->parent, $ids ) ) {
            foreach ( $this->map as $slug => $data ) {
                $id = $data['id'];
                if ( $term->parent == $id ) {
                    return home_url() . '/' . $slug . '/' . $term->slug;
                }
            }
        }

        return $link;
    }

    public function filter_post_link( $link, $post ) {

        // Get this post's categories.
        $terms = wp_get_object_terms( $post->ID, 'category' );

        // If no categories, return link.
        if ( empty( $terms ) ) {
            return $link;
        }

        foreach( $terms as $term ) {
            foreach( $this->map as $slug => $data ) {
                $term_id = $data['id'];

                // Match parent level.
                if ( $term_id == $term->term_id ) {
                    return home_url() . '/' . $term->slug . '/' . $post->post_name;
                }

                if ( $term_id == $term->parent ) {
                    $parent = static::get_category( $term->parent );
                    return home_url() . '/' . $parent->slug . '/' . $term->slug . '/' . $post->post_name;
                }
            }
        }

        return $link;
    }

    ////////////////

    /**
     * @var array Cached get_by_term fetches.
     */
    static $terms = [];

    /**
     * Get object cached get_term_by() requests.
     * @param $slug_or_id
     * @return \WP_Term
     */
    static function get_category( $slug_or_id ) {
        foreach( static::$terms as $category ) {
            if ( $slug_or_id == $category->term_id || $slug_or_id == $category->slug ) {
                return $category;
            }
        }
        if ( is_numeric( $slug_or_id ) ) {
            $term = get_term_by( 'id', $slug_or_id, static::$taxonomy );
        } else {
            $term = get_term_by( 'slug', $slug_or_id, static::$taxonomy );
        }
        static::$terms[] = $term;
        return $term;
    }
}
