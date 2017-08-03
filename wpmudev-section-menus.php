<?php
/**
 * Plugin Name: WPMU DEV Section Menus
 * Plugin URI: https://github.com/rachelmccollin/wpmudev-section-menus
 * Description: Plugin to Automatically Display Section Menus in WordPress
 * Version: 1.0
 * Author: Rachel McCollin
 * Author URI: http://rachelmccollin.co.uk
 *
 */
 
 /*******************************************************************************
function wpmu_check_for_top_page() - identifies the top level page relating to the current page.
*******************************************************************************/
function wpmu_check_for_top_page() {
	
	global $post;

	// check if the page has parents
	if ( $post->post_parent ){
	
		// fetch the list of ancestors
		$parents = array_reverse( get_post_ancestors( $post->ID ) );
		
		// get the top level ancestor
		return $parents[0];
		
	}
	
	// return the id  - this will be the topmost ancestor if there is one, or the current page if not
	return $post->ID;
		

}

/*******************************************************************************
function wpmu_list_subpages() - lists the $ancestor page and its supbages
*******************************************************************************/
function wpmu_list_subpages() {

	// run the wpmu_check_for_page_tree function to fetch top level page
	$ancestor = wpmu_check_for_top_page();
	
	// set the arguments for children of the ancestor page
	$args = array(
		'child_of' => $ancestor,
		'depth' => '-1',
		'title_li' => '',
	);
			
	// set a value for get_pages to check if it's empty
	$list_pages = get_pages( $args );
	
	// check if $list_pages has values
	if( $list_pages ) {
	
		// open a list with the ancestor page at the top
		?>
		<ul class="page-tree">
			<?php // list ancestor page ?>
			<li class="ancestor">
				<a href="<?php echo get_permalink( $ancestor ); ?>"><?php echo get_the_title( $ancestor ); ?></a>
			</li>
			
			<?php
			// use wp_list_pages to list subpages of ancestor or current page
			wp_list_pages( $args );
		
	
		// close the page-tree list
		?>
		</ul>
	
	<?php
	}

}

/*********************************************************************************
Widget
*********************************************************************************/
class Wpmu_Section_Menus_Widget extends WP_Widget {
	//widget constructor function
	function __construct() {
		$widget_options = array(
			'classname' => 'wpmu_section_menus_widget',
			'description' => 'Display a list of pages in the current section of the website.'
		);
		parent::__construct( 'wpmu_section_menus_widget', 'Section Menu', $widget_options );
	}
 
	//function to define the form in the Widgets screen
	function form( $instance ) { 
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$tel = ! empty( $instance['tel'] ) ? $instance['tel'] : 'Telephone number';
		$email = ! empty( $instance['email'] ) ? $instance['email'] : 'Email address';
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input class="widefat" type ="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
	
	<?php }

 	//function to define the data saved by the widget
	function update( $new_instance, $old_instance ) { 
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		return $instance;
	}

 	//function to display the widget in the site
	function widget( $args, $instance ) {
		
			if ( is_page() && ! is_front_page() ) {
				
				echo $args ['before_widget'];
		
				$title = apply_filters( 'widget_title', $instance[ 'title' ] );
			?>
				<aside class="section-menu">
					<?php if ( ! empty( $title ) ) {
						echo $before_title . $title . $after_title; 
					}; ?>
					<?php  wpmu_list_subpages() ?>
				</aside>
				
				<?php echo $args['after_widget'];
				
			}		
				
	}

}

//function to register the widget
function wpmu_register_section_menu_widget() { 
	register_widget( 'Wpmu_Section_Menus_Widget' );
}
add_action( 'widgets_init', 'wpmu_register_section_menu_widget' );