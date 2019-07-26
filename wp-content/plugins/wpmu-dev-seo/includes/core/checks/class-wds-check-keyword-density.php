<?php

class Smartcrawl_Check_Keyword_Density extends Smartcrawl_Check_Abstract {

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;

	private $_density = null;

	public function get_status_msg() {
		return false === $this->_state
			? sprintf( __( 'Keyword density is less than %1$d%%', 'wds' ), $this->get_min() )
			: sprintf( __( 'Keyword density is more than %1$d%%', 'wds' ), $this->get_min() );
	}

	public function get_min() {
		return 2;
	}

	public function apply() {
		$markup = $this->get_markup();
		if ( empty( $markup ) ) {
			$this->_state = false;

			return ! ! $this->_state;
		}

		$kws = $this->get_focus();
		if ( empty( $kws ) ) {
			$this->_state = true;

			return ! ! $this->_state; // Can't determine kw density
		}
		$words = Smartcrawl_String::words( Smartcrawl_Html::plaintext( $markup ) );
		$freq = array_count_values( $words );
		$densities = array();
		if ( ! empty( $words ) ) {
			foreach ( $kws as $kw ) {
				$dns = isset( $freq[ $kw ] ) ? $freq[ $kw ] : 0;
				$densities[ $kw ] = ( $dns / count( $words ) ) * 100;
			}
		}
		$density = ! empty( $densities )
			? array_sum( array_values( $densities ) ) / count( $densities )
			: 0;
		$this->_density = $density;

		$this->_state = $density >= $this->get_min();

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		$keyword_density = $this->_density ? round( $this->_density, 2 ) : 0;

		if ( 0 === $keyword_density ) {
			$message = __( 'Currently you haven\'t used any keywords in your content. The minimum recommended density is %1$s%%. A low keyword density means your content has less chance of ranking highly for your chosen focus keywords.', 'wds' );
		} elseif ( $this->_state ) {
			$message = __( 'Your keyword density is %2$s%1$% which is greater than the minimum value of %1$s%1$%, nice work! This means your content has a better chance of ranking highly for your chosen focus keywords, without appearing as spam.', 'wds' );
		} else {
			$message = __( 'Currently your keyword density is %2$s%1$% which is below the recommended %1$s%1$%. A low keyword density means your content has less chance of ranking highly for your chosen focus keywords.', 'wds' );
		}

		return sprintf(
			$message,
			$this->get_min(),
			$keyword_density
		);
	}

	public function get_more_info() {
		$message = __( 'Keyword density is all about making sure your content has enough keywords in it that it has a higher chance of appearing in the first few search results for your focus keywords. One way of making sure people will be able to find our content is using particular focus keywords, and using them as much as naturally possible in our content. In doing this we are trying to match up the keywords that people are likely to use when searching for this article or page, so try to get into your visitors mind and picture them typing a search into Google. While we recommend aiming for %1$s%% density, remember content is king and you don\'t your article ending up sounding like a robot. Get creative and utilize the page title, image caption and subheadings.', 'wds' );

		return sprintf(
			$message,
			$this->get_min()
		);
	}
}
