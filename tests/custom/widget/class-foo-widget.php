<?php
namespace Fulcrum\Test\Custom\Widget;

use Fulcrum\Custom\Widget\Widget;

class Foo_Widget extends Widget {
	
	/**
	 * Render the HTML for the widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Display arguments including
	 *                          before_title, after_title, before_widget, & after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 *
	 * @return null
	 */
	protected function render_widget( array &$args, array &$instance ) {
		if ( is_readable( $this->config->view ) ) {
			include( $this->config->view );
		}
	}


	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If false is returned, the instance won't be saved / updated.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['class'] = strip_tags( $new_instance['class'] );

		return $new_instance;
	}
}