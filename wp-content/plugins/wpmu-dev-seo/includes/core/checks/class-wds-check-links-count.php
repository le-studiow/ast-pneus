<?php

class Smartcrawl_Check_Links_Count extends Smartcrawl_Check_Abstract {

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;
	private $_link_count = null;

	public function get_status_msg() {
		$link_count = $this->_link_count ? $this->_link_count : 0;
		$links_found_string = $link_count > 1 ? esc_html__( '%d links discovered', 'wds' ) : esc_html__( '%d link discovered', 'wds' );

		return false === $this->_state || $link_count <= 0
			? __( 'No internal or external links', 'wds' )
			: sprintf(
				$links_found_string,
				$link_count
			);
	}

	public function apply() {
		$selector_links = 'a[href]';
		$selector_anchors = 'a[href^="#"]';

		$links = Smartcrawl_Html::find( $selector_links, $this->get_markup() );
		$anchors = Smartcrawl_Html::find( $selector_anchors, $this->get_markup() );
		$link_count = count( $links );
		$this->_link_count = $link_count;

		$this->_state = $link_count && $link_count > count( $anchors );

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		$link_count = $this->_link_count ? $this->_link_count : 0;
		if ( $this->_state ) {
			$message = __( 'Internal links help search engines crawl your website, effectively pointing them to more pages to index on your website. You\'ve already added %1$d link(s), nice work!', 'wds' );
		} else {
			$message = __( 'Internal links help search engines crawl your website, effectively pointing them to more pages to index on your website. You should consider adding at least one internal link to another related article.', 'wds' );
		}

		return sprintf(
			$message,
			$link_count
		);
	}

	public function get_more_info() {
		ob_start();
		?>
		<?php esc_html_e( "Internal links are important for linking together related content. Search engines will 'crawl' through your website, indexing pages and posts as they go. To help them discover all the juicy content your website has to offer, it's wise to make sure your content has internal links built into for the bot to follow and index.", 'wds' ); ?>
		<br/><br/>

		<?php printf(
			"%s <a href='https://moz.com/learn/seo/internal-link' target='_blank'>https://moz.com/learn/seo/internal-link</a>",
			esc_html__( "External links don't benefit your SEO by having them in your own content, but you'll want to try and get as many other websites linking to your articles and pages as possible. Search engines treat links to your website as a 'third party vote' in favour of your website - like a vote of confidence. Since these the hardest form of 'validation' to get (another website has to endorse you!) search engines weigh them heavily when considering page rank. For more info:", 'wds' )
		); ?>
		<br/><br/>

		<?php esc_html_e( "Note: This check is only looking at the content your page is outputting and doesn't include your main navigation. Blogs with lots of posts will benefit the most from this method, as it aids Google in finding and indexing all of your content, not just the latest articles.", 'wds' ); ?>
		<?php
		return ob_get_clean();
	}
}
