<?php
if(!class_exists('Gift_Post_Texonomy_Template'))
{
	class Gift_Post_Texonomy_Template
	{
		
	   	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
    		
    	} // END

    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Texonomy
    		$this->create_gift_taxonomies();
    		
    	} // END init()

    	/**
    	 * Create the Gift Texonomy
    	 */
    	function create_gift_taxonomies() 
		{
	
			// Add new taxonomy, NOT hierarchical (like tags)
			$labels = array(
				'name'              => _x( 'Gift Type', 'taxonomy general name' ),
				'singular_name'     => _x( 'Gift', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Gifts' ),
				'all_items'         => __( 'All Gifts' ),
				'parent_item'       => __( 'Parent Gift' ),
				'parent_item_colon' => __( 'Parent Gift:' ),
				'edit_item'         => __( 'Edit Gift' ),
				'update_item'       => __( 'Update Gift' ),
				'add_new_item'      => __( 'Add New Gift' ),
				'new_item_name'     => __( 'New Gift Name' ),
				'menu_name'         => __( 'Gift Type' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'gift' ),
			);

			//register_taxonomy( 'gift', array( 'gift' ), $args );
			register_taxonomy( 'gifts', 'gift-post', $args );
		}	
  
	} // END class
}// END if
