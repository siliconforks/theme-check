<?php
class Style_Tags implements themecheck {
	protected $error = array();

	protected $tags = array();

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->tags = $data['theme']['Tags'];
		}
	}

	function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		if ( ! $this->tags ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( '<strong>Tags:</strong> is either empty or missing in style.css header.', 'theme-check' )
			);
		} else {
			$deprecated_tags    = $this->get_deprecated_tags();
			$allowed_tags       = $this->get_allowed_tags();
			$subject_tags       = $this->get_subject_tags();
			$subject_tags_count = 0;
			$subject_tags_name  = '';

			foreach ( $this->tags as $tag ) {

				if ( strpos( strtolower( $tag ), 'accessibility-ready' ) !== false ) {
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span>: %s',
						__( 'INFO', 'theme-check' ),
						__( 'Themes that use the tag accessibility-ready will need to undergo an accessibility review.', 'theme-check' ) . ' ' . __( 'See <a href="https://make.wordpress.org/themes/handbook/review/accessibility/">https://make.wordpress.org/themes/handbook/review/accessibility/</a>', 'theme-check' )
					);
				}

				if ( ! in_array( strtolower( $tag ), $allowed_tags ) ) {
					if ( in_array( strtolower( $tag ), $deprecated_tags ) ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'The tag %s has been deprecated, please remove it from your style.css header.', 'theme-check' ),
								'<strong>' . $tag . '</strong>'
							)
						);
						$ret           = false;
					} else {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'Found wrong tag, remove %s from your style.css header.', 'theme-check' ),
								'<strong>' . $tag . '</strong>'
							)
						);
						$ret           = false;
					}
				}

				if ( in_array( strtolower( $tag ), $subject_tags ) ) {
					$subject_tags_name .= strtolower( $tag ) . ', ';
					$subject_tags_count++;
				}

				if ( in_array( strtolower( $tag ), $allowed_tags ) ) {
					if ( count( array_keys( $this->tags, $tag ) ) > 1 ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'The tag %s is being used more than once, please remove it from your style.css header.', 'theme-check' ),
								'<strong>' . $tag . '</strong>'
							)
						);
						$ret           = false;
					}
				}
			}

			if ( $subject_tags_count > 3 ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'A maximum of 3 subject tags are allowed. The theme has %1$u subjects tags ( %2$s ). Please remove the subject tags, which do not directly apply to the theme.', 'theme-check' ),
						$subject_tags_count,
						'<strong>' . rtrim( $subject_tags_name, ', ' ) . '</strong>'
					),
					sprintf(
						'<a href="%s">%s</a>',
						'https://make.wordpress.org/themes/handbook/review/required/theme-tags/',
						__( 'See Theme Tags', 'theme-check' )
					)
				);
				$ret           = false;
			}
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}

	/**
	 * Get full list of allowed tags - including subject tags.
	 *
	 * @return array
	 */
	private function get_allowed_tags() {
		$allowed_tags = array(
			'grid-layout',
			'one-column',
			'two-columns',
			'three-columns',
			'four-columns',
			'left-sidebar',
			'right-sidebar',
			'wide-blocks',
			'flexible-header',
			'footer-widgets',
			'accessibility-ready',
			'block-patterns',
			'block-styles',
			'buddypress',
			'custom-background',
			'custom-colors',
			'custom-header',
			'custom-logo',
			'custom-menu',
			'editor-style',
			'featured-image-header',
			'featured-images',
			'front-page-post-form',
			'full-width-template',
			'full-site-editing',
			'microformats',
			'post-formats',
			'rtl-language-support',
			'sticky-post',
			'theme-options',
			'threaded-comments',
			'translation-ready',
		);
		return array_merge( $allowed_tags, self::get_subject_tags() );
	}

	/**
	 * Get the list of subject tags.
	 *
	 * @return array
	 */
	private function get_subject_tags() {
		return array(
			'blog',
			'e-commerce',
			'education',
			'entertainment',
			'food-and-drink',
			'holiday',
			'news',
			'photography',
			'portfolio',
		);
	}

	private function get_deprecated_tags() {
		return array(
			'flexible-width',
			'fixed-width',
			'black',
			'blue',
			'brown',
			'gray',
			'green',
			'orange',
			'pink',
			'purple',
			'red',
			'silver',
			'tan',
			'white',
			'yellow',
			'dark',
			'light',
			'fixed-layout',
			'fluid-layout',
			'responsive-layout',
			'blavatar',
			'holiday',
			'photoblogging',
			'seasonal',
		);
	}
}
$themechecks[] = new Style_Tags();
