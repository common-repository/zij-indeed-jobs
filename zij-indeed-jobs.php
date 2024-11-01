<?php
/*
Plugin Name: Zij Indeed Jobs
Plugin URI: http://zijsoft.com/wordpress/zijindeedjobs
Description: Zijsoft provide the indeed jobs integeration into your wordpress installation easily
Author: Habib Ahmed
Version: 1.2
Author URI: http://zijsoft.com/aboutus
*/

/**
 * Adds Zij indeed jobs widget.
 */
class ZijIndeedJobs extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'zijindeedjobs', // Base ID
			__( 'Zij Indeed Jobs', 'zij-indeed-jobs' ), // Name
			array( 'description' => __( 'Zij integrate indeed jobs into your wordpress installation', 'zij-indeed-jobs' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
        require_once('indeedlib.php');
        if(!empty($instance['zijindeed_apikey'])){
            $client = new Indeed($instance['zijindeed_apikey']);
            $params = array(
                "q" => $instance['zijindeed_category'],
                "l" => $instance['zijindeed_location'],
                "start" => 0,
                "jt" => $instance['zijindeed_jobtype'],
                "limit" => $instance['zijindeed_limit'],
                "userip" => '127.0.0.1',
                "useragent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2)"
            );

            $indeedjobs = $client->search($params);
        }
        if(isset($indeedjobs)){
        	foreach($indeedjobs['results'] AS $job){
        		$html = '<div class="zijindeed_job_wrapper">
        					<div class="zijindeed_job_upper">
        						<a class="zijindeed_title" href="'.$job['url'].'" target="_blank" >'.$job['jobtitle'].'</a>
        						<span class="zijindeed_job_comp_loc">'.$job['company'].' - '.$job['formattedLocationFull'].'</span>
        						<div class="zijindeed_snippet">'.$job['snippet'].'</div>
    						</div>
    						<div class="zijindeed_job_lower">
    							<span class="fa fa-calendar"></span>'.date('l, d M Y', strtotime($job['date'])).'
    							<a class="zijindeed_job_applynow" href="'.$job['url'].'" target="_blank" >'.__('Apply','zijindeedjobs').'</a>
    						</div>
        				</div>';
				echo $html;
        	}
        }
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Zij Indeed Jobs', 'zij-indeed-jobs' );
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'title' ).'">'.__('Title:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'">';
		echo '</p>';
		$zijindeed_apikey = ! empty( $instance['zijindeed_apikey'] ) ? $instance['zijindeed_apikey'] : '4523189466224463';
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'zijindeed_apikey' ).'">'.__('zijindeed_apikey:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'zijindeed_apikey' ).'" name="'.$this->get_field_name( 'zijindeed_apikey' ).'" type="text" value="'.esc_attr( $zijindeed_apikey ).'">';
		echo '</p>';
		$zijindeed_category = ! empty( $instance['zijindeed_category'] ) ? $instance['zijindeed_category'] : 'PHP Developer';
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'zijindeed_category' ).'">'.__('zijindeed_category:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'zijindeed_category' ).'" name="'.$this->get_field_name( 'zijindeed_category' ).'" type="text" value="'.esc_attr( $zijindeed_category ).'">';
		echo '</p>';
		$zijindeed_location = ! empty( $instance['zijindeed_location'] ) ? $instance['zijindeed_location'] : 'austin';
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'zijindeed_location' ).'">'.__('zijindeed_location:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'zijindeed_location' ).'" name="'.$this->get_field_name( 'zijindeed_location' ).'" type="text" value="'.esc_attr( $zijindeed_location ).'">';
		echo '</p>';
		$zijindeed_jobtype = ! empty( $instance['zijindeed_jobtype'] ) ? $instance['zijindeed_jobtype'] : 'Full-time';
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'zijindeed_jobtype' ).'">'.__('zijindeed_jobtype:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'zijindeed_jobtype' ).'" name="'.$this->get_field_name( 'zijindeed_jobtype' ).'" type="text" value="'.esc_attr( $zijindeed_jobtype ).'">';
		echo '</p>';
		$zijindeed_limit = ! empty( $instance['zijindeed_limit'] ) ? $instance['zijindeed_limit'] : 10;
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'zijindeed_limit' ).'">'.__('zijindeed_limit:','zij-indeed-jobs').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'zijindeed_limit' ).'" name="'.$this->get_field_name( 'zijindeed_limit' ).'" type="text" value="'.esc_attr( $zijindeed_limit ).'">';
		echo '</p>';
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['zijindeed_apikey'] = ( ! empty( $new_instance['zijindeed_apikey'] ) ) ? strip_tags( $new_instance['zijindeed_apikey'] ) : '';
		$instance['zijindeed_category'] = ( ! empty( $new_instance['zijindeed_category'] ) ) ? strip_tags( $new_instance['zijindeed_category'] ) : '';
		$instance['zijindeed_location'] = ( ! empty( $new_instance['zijindeed_location'] ) ) ? strip_tags( $new_instance['zijindeed_location'] ) : '';
		$instance['zijindeed_jobtype'] = ( ! empty( $new_instance['zijindeed_jobtype'] ) ) ? strip_tags( $new_instance['zijindeed_jobtype'] ) : '';
		$instance['zijindeed_limit'] = ( ! empty( $new_instance['zijindeed_limity'] ) ) ? strip_tags( $new_instance['zijindeed_limitry'] ) : '';

		return $instance;
	}

} // class Foo_Widget


function zijindeed_stylesheet() {
    wp_enqueue_style( 'zijindeed-style', plugins_url( 'includes/style.css', __FILE__ ) );
    wp_enqueue_style('zijindeed-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'); 
}
add_action( 'wp_enqueue_scripts', 'zijindeed_stylesheet' );

// register Zij indeed jobs widget
function register_zijindeedjobswidget() {
    register_widget( 'ZijIndeedJobs' );
}
add_action( 'widgets_init', 'register_zijindeedjobswidget' );

?>