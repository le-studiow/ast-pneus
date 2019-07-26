<?php

class Smartcrawl_Check_Keywords_Used extends Smartcrawl_Check_Post_Abstract {

	/**
	 * Holds post IDs where the focus keywords have been used before
	 *
	 * @var array
	 */
	private $_pids = array();

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;

	public function get_status_msg() {
		return false === $this->_state
			? __( 'Keywords have been previously used before', 'wds' )
			: __( 'Keywords have not been used before', 'wds' );
	}

	public function apply() {
		$queries = array( 'relation' => 'OR' );
		$kws = $this->get_focus();
		if ( empty( $kws ) ) {
			return true;
		}

		foreach ( $kws as $kw ) {
			$queries[] = array(
				'key'     => '_wds_focus-keywords',
				'value'   => $kw,
				'compare' => 'LIKE',
			);
		}

		$post_id = $this->get_post_id();
		$post_ids[] = $post_id;
		if ( wp_is_post_revision( $post_id ) ) {
			$post_ids[] = wp_is_post_revision( $post_id );
		}

		$query = new WP_Query( array(
			'post_type'    => 'any',
			'fields'       => 'ids',
			'post__not_in' => $post_ids,
			'meta_query'   => array(
				$queries,
			),
		) );

		$this->_state = $query->post_count <= 0;

		if ( ! $this->_state ) {
			$this->_pids = $query->posts;

			return false;
		}

		return true;
	}

	public function get_post_id() {
		$subject = $this->get_subject();

		return is_object( $subject ) ? $subject->ID : $subject;
	}

	public function get_recommendation() {
		if ( $this->_state ) {
			$message = __( "Focus keywords are intended to be unique so you're less likely to write duplicate content. So far all your focus keywords are unique, way to go!", 'wds' );
		} else {
			$message = __( "Focus keywords are intended to be unique so you're less likely to write duplicate content. Consider changing the focus keywords to something unique.", 'wds' );
		}

		return $message;
	}

	public function get_more_info() {
		ob_start();
		?>
		<?php esc_html_e( "Whilst duplicate content isn't technically penalized it presents three rather niggly issues for search engines:", 'wds' ); ?>
		<br/><br/>

		<?php esc_html_e( "1. It's unclear which versions to include/exclude from their indexes.", 'wds' ); ?><br/>
		<?php esc_html_e( "2. They don't know whether to direct the link metrics (trust, authority, anchor text, link equity, etc.) to one page, or keep it separated between multiple versions.", 'wds' ); ?>
		<br/>
		<?php esc_html_e( '3. The engine is unsure which versions to rank for query results.', 'wds' ); ?><br/><br/>

		<?php esc_html_e( "So whilst there's no direct penalty, if your content isn't unique then search engine algorithms could be filtering out your articles from their results. The easiest way to make sure this doesn't happen is to try and make each of your posts and pages as unique as possible, hence specifying different focus keywords for each article you write.", 'wds' ); ?>
		<br/><br/>

		<?php printf(
			"%s <a href='https://premium.wpmudev.org/blog/wordpress-canonicalization-guide/' target='_blank'>%s</a>.",
			esc_html__( "Note: If you happen to have two pages with the same content, it's important to tell search engines which one to show in search results using the Canonical URL feature. You can read more about this", 'wds' ),
			esc_html__( 'here' )
		); ?>
		<?php
		return ob_get_clean();
	}

}
