<?php

/**
 * The helper class
 *
 * Contains various functions and utilities to ease development
 */
class CutlassHelper {

	/**
	 * get_title
	 *
	 * Returns a nice formatted title according to which page
	 * we're on.
	 *
	 * From Root's Sage
	 * https://github.com/roots/sage
	 *
	 * return @string
	 */
	public static function get_title() {

		if (is_home()) {
			if (get_option('page_for_posts', true)) {
				return get_the_title(get_option('page_for_posts', true));
			} else {
				return 'Latest Posts';
			}
		} elseif (is_archive()) {
			return get_the_archive_title();
		} elseif (is_search()) {
			return 'Search Results for ' . get_search_query();
		} elseif (is_404()) {
			return 'Not Found';
		} else {
			return get_the_title();
		}

	}

	/**
	 * get_posts
	 *
	 * Checks global wp_query for posts and returns them,
	 * otherwise runs get_posts on passed query
	 *
	 * @param array $query
	 *
	 * @return array
	 */
	public static function get_posts($query = array()) {
		global $wp_query;

		/**
		 * Set return var
		 */
		$posts = array();

		/**
		 * If the query's empty and the global WP_Query has posts grab them
		 * else just grab the posts the normal way
		 */
		if( empty($query) && property_exists($wp_query, 'posts'))
			$posts = $wp_query->posts;
		else
			$posts = get_posts($query);

		/**
		 * Return empty if either of those fail
		 */
		if( empty($posts) )
			return array();

		/**
		 * Convert WP_Posts to CutlassPosts
		 */
		CutlassHelper::convert_posts($posts);

		/**
		 * Return array of CutlassPosts
		 */
		return $posts;

	}

	/**
	 * get_post
	 *
	 * Gets the post and converts it into a CutlassPost
	 * which grants us some nifty methods and properties
	 *
	 * @param int $postid
	 *
	 * @return array|null
	 */
	public static function get_post( $postid ) {

		/**
		 * Grab post using postid
		 */
		$post = get_post($postid);

		/**
		 * If it's a correct WP_Post convert it to a
		 * CutlassPost
		 */
		if ( is_a($post, 'WP_Post'))
			return new CutlassPost($post);

		/**
		 * Return null if all else fails
		 */
		return null;

	}

	/**
	 * convert_posts
	 *
	 * Converts WP_Posts to CutlassPosts
	 *
	 * * Note: We use array_walk over foreach for memory conservation because
	 * * the gained time is not worth the memory lost
	 *
	 * @param array $posts
	 */
	public static function convert_posts(&$posts) {

		array_walk($posts, function(&$value, $key) {
			$value = new CutlassPost($value);
		});

	}
}