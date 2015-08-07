<?php
add_action('widgets_init', create_function('', 'return register_widget("WidgetRotatorPro");'));
class WidgetRotatorPro extends WP_Widget{
	var $id_base = 'widget-rotator-pro';
	function __construct(){
		add_action('in_widget_form', array( $this, 'add_widget_rotator_options' ), 10, 3);
		add_filter('widget_update_callback', array( $this, 'widget_update_callback' ), 10, 3);
		add_filter('widget_display_callback', array( $this, 'filter_widget' ), 1, 2);
		parent::WP_Widget($this->id_base,'Widget Rotator Pro');
		add_action( 'admin_init', array( $this, 'js_scripts' ) );
		add_action('admin_menu',array($this,'add_menu_pages'));
	}

	function add_menu_pages(){

		add_menu_page('Widget Rotator Pro', 'Widget Rotator Pro', 'manage_options','widget_rotator_pro_settings_page',  array($this,'settings_page'));

		$menu=add_submenu_page('widget_rotator_pro_settings_page', 'Settings Page', 'Settings Page', 'manage_options','widget_rotator_pro_settings_page',  array($this,'settings_page'));

		add_action( "admin_action_widget_rotator_pro_settings_page", array($this, 'settings_page') );


	}

	function plugin_path($path=''){
		$plugin_path = dirname(__FILE__);
		return $plugin_path.'/'.trim($path,'/');
	}

	function plugin_url($path=''){
		$plugin_url = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__));
		return $plugin_url.'/'.trim($path,'/');
	}

	function asset_url($path=''){
		return $this->plugin_url('assets/'.$path);
	}

	function js_scripts(){
		wp_enqueue_script('widget_rotator_pro_js', $this->asset_url('js/widget_rotator_pro.js'),array('jquery'),false,true);
	}


	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags(stripslashes($new_instance['title']));
		$instance['display_order'] = $new_instance['display_order'];
		return $instance;
    }

	function form($instance) {
		$defaults = array( 'title' => $this->number, 'display_order'=>'random' );

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$title = wp_strip_all_tags(stripslashes($instance['title']));
		$display_order = $instance['display_order'];

	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('display_order'); ?>"><?php _e('Display Order:'); ?></label></p>
		<p>Random <input id="<?php echo $this->get_field_id('display_order'); ?>" name="<?php echo $this->get_field_name('display_order'); ?>" type="radio" value="random" <?php checked($display_order,'random');?> /> Sequential <input id="<?php echo $this->get_field_id('display_order'); ?>" name="<?php echo $this->get_field_name('display_order'); ?>" type="radio" value="sequential" <?php checked($display_order,'sequential');?> /></p>
		<p class="howto">Please <a href="<?php echo self_admin_url('widgets.php');?>">refresh this page</a> after save settings.</p>
	<?php

	}

	function get_settings_by_callback($callback){
		$settings = @call_user_func(array($callback[0],'get_settings'));
		if(!$settings)
			return false;
		               
		return $settings[$callback[0]->number];
		     
	}

	function widget($args, $instance) {
		
		global $wp_registered_widgets,$wp_registered_sidebars;

		$index = $args['id'];
		     
		$sidebars_widgets = wp_get_sidebars_widgets();
		
		$sidebar = $wp_registered_sidebars[$index];
		$callbacks = array();
		
		 
		foreach ( (array) $sidebars_widgets[$index] as $id ) {

			if(false !== strpos($id,$this->id_base))
				continue;

			$callback = $wp_registered_widgets[$id]['callback'];
			
			     
			if ( !is_callable($callback) ) 
				continue;
			
			if(!$settings = $this->get_settings_by_callback($callback))
				continue;
			                                   
			               
			if(!$widget_rotator_id = $settings['rotate_in'])
				continue;

			$params = array_merge(
				array( array_merge( $sidebar, array('id_base' => $id, 'id_base' => $wp_registered_widgets[$id]['name']) ) ),
				(array) $wp_registered_widgets[$id]['params']
			);
			
			$classname_ = '';
			foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			}
			$classname_ = ltrim($classname_, '_');
			$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

			$params = apply_filters( 'dynamic_sidebar_params', $params );
			

			do_action( 'dynamic_sidebar', $wp_registered_widgets[$id] );
			             
			$callbacks[$widget_rotator_id][] = array('callback'=>$callback,'params'=>$params);
			     
		}
		     
		if($callbacks)
			$this->display_widget($callbacks);

	}



	function display_widget($callbacks){
		foreach($callbacks as $widget_rotator_id => $cs){
			$settings = $this->get_widget_rotator_settings($widget_rotator_id);
			$display_order = $settings['display_order'];
			
			if($display_order === 'sequential'){
				$this->display_seq_widget($cs,$widget_rotator_id);
			}else{
				$this->display_random_widget($cs);
			}
		}
		
	}

	function get_widget_rotator_settings($id){
		$settings = get_option('widget_'.$this->id_base);
		return $settings[$id];
	}

	

	function display_random_widget($callbacks){

		$ids = array();
		
		foreach($callbacks as $id => $callback){
			
			$settings = $this->get_settings_by_callback($callback['callback']);
			$random_priority = 0;
			if($settings['random_priority'] != 0)
				$random_priority = absint($settings['random_priority']);
			if($random_priority === 0)
				continue;
			     
			$pads = array_pad(array(), $random_priority, $id);
			$ids = array_merge($ids,$pads);
		}
		
		                                   
		$id = $ids[array_rand($ids,1)];

		$this->_display_widget($id,$callbacks);
	}

	function get_last_widget_id($widget_rotator_id){
		     
		if(isset($_COOKIE["widget_rotator_pro_last_".$widget_rotator_id]))
			return $_COOKIE["widget_rotator_pro_last_".$widget_rotator_id];
		return false;

		
	}

	function display_seq_widget($callbacks,$widget_rotator_id){
		
		     
		$ids = array_keys($callbacks);
		$ids = array_merge($ids,$ids);
		     
		$idx = 0;
		$last_widget_id = $this->get_last_widget_id($widget_rotator_id);

        if( $last_widget_id !== false ){                                        
			foreach($ids as $k => $v){
				if($v == $last_widget_id ){
					$idx = $k +1;
					break;
				}
			}
		} 
		     
		$id = $ids[$idx];
		
		$this->_display_widget($id,$callbacks);
		
		echo "\n<script>\ndocument.cookie='widget_rotator_pro_last_".$widget_rotator_id."=".$id."; max-age=86400; path=".SITECOOKIEPATH."';\n</script>\n";
		
	}

	function _display_widget($id,$callbacks){
		$func = $callbacks[$id];
		call_user_func_array($func['callback'],$func['params']);
	}


	function filter_widget($instance, $widget){
		
		if($widget->id_base == $this->id_base){
			$this->set_rotator_displayed($widget->number);
			return $instance;
		}
				                    		                    		                    		               
		if ( $widget_rotator_id = $instance['rotate_in']  ){
			               
			if(!$this->is_rotator_displayed($widget_rotator_id))
				return false;
			     
			if($this->is_widget_rotated($widget_rotator_id) === true)
				return false;

			$this->set_widget_rotated($widget_rotator_id);
		}
		     
		return $instance;

	}

	function set_rotator_displayed($widget_rotator_id){
		$this->displayed[$widget_rotator_id]=true;
	}

	function is_rotator_displayed($widget_rotator_id){
		return $this->displayed[$widget_rotator_id];
	}

	function set_widget_rotated($widget_rotator_id){
		$this->rotated[$widget_rotator_id] = true;
	}

	function is_widget_rotated($widget_rotator_id){
		return $this->rotated[$widget_rotator_id];
	}


	function add_widget_rotator_options($widget, $return, $instance) {
		     
		if($widget->id_base === $this->id_base) 
			return;


		$defaults = array(
			'rotate_in' => -1,
			'random_priority' => 1,
		);

		$instance = wp_parse_args( $instance, $defaults );
		     
		extract( $instance, EXTR_SKIP );

		$this->print_rotate_in($rotate_in);
		
		$this->print_random_priority($random_priority);

	}

	function print_rotate_in($rotate_in){
		$vars = get_option('widget_'.$this->id_base);

		$vars[-1] = array('title'=>'Do Not Rotate');

		echo '<p>Rotate this widget in <select name="rotate_in">';
		               
		foreach($vars as $id => $var){
			if(empty($var['title']))
				continue;
			if($id !== -1 && !$this->is_active_widget_rotator($id))
				continue;

			echo '<option value="'.$id.'"'.selected( $rotate_in, $id,false ).'>';
			echo $var['title'];
			echo '</option>';
		}
		echo '</select>';

		echo '</p>';
	}

	function print_random_priority($random_priority){

		echo '<label>Random Priority (1-100)</label>';
		echo '<p><input size="3" type="text" class="random_priority" name="random_priority" value="'.$random_priority.'"></p>';
		echo '<p class="howto">Please make sure the sum is 100.</p>';

		// $nums = range(0,100,5);
		// $nums[0] = 1;

		// echo '<p>Random Priority <select name="random_priority">';
		
		// foreach($nums as $num){
			
		// 	echo '<option value="'.$num.'"'.selected( $random_priority, $num,false ).'>';
		// 	echo $num;
		// 	echo '</option>';
		// }
		// echo '</select>';
		// echo '</p>';
	}

	function is_active_widget_rotator($id){
		$sidebars_widgets = wp_get_sidebars_widgets();
		     
		unset($sidebars_widgets['wp_inactive_widgets']);
		
		$id = $this->id_base.'-'.$id;
		foreach($sidebars_widgets as $widgets){
			if(in_array($id,$widgets))
				return true;
			     
		}
		return false;
	}

	function widget_update_callback($instance, $new_instance, $old_instance){
		
		     
		$instance['rotate_in'] = wp_strip_all_tags($_POST['rotate_in'] );
		$instance['random_priority'] = absint($_POST['random_priority']);
	    	
    	return $instance;

	}

	function settings_page(){
		
		include($this->plugin_path('widget_rotator_pro_settings_page.php'));
	}

}




?>
