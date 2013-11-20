<?php
/*
	Plugin Name: admin db inject
	Plugin URI: http://o.o.com
	Description: This Plugin Create new wp_ table when it's enable, and you can insert text into the field of the new DB
	Author: dani k		
	Author URI: http://o.o.com
	
	*/

///CREATE THE DB   ////////////////


register_activation_hook( __FILE__, 'my_plugin_create_table' );
function my_plugin_create_table()
{
	$my_table_name = $wpdb->prefix . 'new_table';
        // do NOT forget this global
	global $wpdb;
	
	
 
	// this if statement makes sure that the table doe not exist already
	 if($wpdb->get_var("show tables like 'new_table'") != 'new_table') 
	{
		$sql = "CREATE TABLE new_table (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		text_name tinytext NOT NULL,
		main text NOT NULL,
		UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
// this hook will cause our creation function to run when the plugin is activated
// register_activation_hook( __FILE__, 'my_plugin_create_table' );


////////////  	END of DB CREATE    ////////

function pu_insert_custom_table()
{
    global $wpdb;
	
	$options = $newoptions = get_option('mycustomplugin_options');

			$title = ($options['title'])	;
			$main=$options['up_text'];
	$wpdb->insert('new_table',
     array(
          'text_name'=>$title,
          'main'=>$main
          
     ),
     array( 
          '%s',
          '%s'
		   )
				);
}

///// db inject gui///////////////

function wp_myCustomPlugin_DB () {
	$newoptions = get_option('mycustomplugin_options');
	$newoptions['title'] = '';
	$newoptions['text'] = '';
}

function wp_myCustomPlugin_add_pages() {
	add_options_page('custom db penal', 'custom db penal', 'switch_themes', __FILE__, 'wp_DBPluginOptions');
}



Function wp_DBPluginOptions () {
	
if(isset($_POST['upDB'])) {
    pu_insert_custom_table();
}

	$options = $newoptions = get_option('mycustomplugin_options');
	// if submitted, process results
if (!empty($_POST["custom_submit"]) ) {
		$newoptions['up_text'] = strip_tags(stripslashes($_POST["up_text"]));
		$newoptions['title'] = strip_tags(stripslashes($_POST["title"]));
	}
	
	// if changes save!
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('mycustomplugin_options', $options);
	}
	
	echo '<form method="post"><div id="settings">';
	echo "<div class=\"wrap\"><h2>My custom DB page</h2>";
	echo '<table class="form-table">';
	// title
	echo '<tr valign="top"><th scope="row">Title</th>';
	echo '<td><input type="text" name="title" value="'.$options['title'].'" size="5"></input><br />Title</td></tr>';
	//text
	echo '<tr valign="top"><th scope="row">text</th>';
	echo '<td><label for="up_text">
		  <input id="up_text" type="textfield" size="36" name="up_text"  />
		   <br />Enter text to save in DB.
		  </label></td>
		  </tr>';
	echo '</table>';
	echo '<input type="hidden" name="custom_submit" value="true"></input>'; ?>
	<p class="submit"><input name="upDB" type="submit" value="Update DB" ></input></p>;
														<?php
	echo '</form>';
	
	
	
	echo '<div> ';
	
	print_r('title: '.$options['title']);
	echo '</br>';
	print_r('main: '.$options['up_text']);
	
	echo '</dev>';
	//pu_insert_custom_table();

}

////////////////////// insert data to db function        ////////////////




////////////// 	make a shortcode to display db data on post//////////////

function bbcodeplugin_db($attr, $content=null)
{
    if( empty( $content ) )
    {
        return '';
    }
 
    return '<strong>' . db_display(). do_shortcode($content) .'</strong>';
}

function register_shortcode()
{
	add_shortcode('db-table', 'bbcodeplugin_db');
}

add_action('init' , 'register_shortcode');



function db_display()
{
			global $wpdb;
			$mtwpvalu = $wpdb->get_results( "SELECT text_name,main FROM new_table" ); 
			//$my_table_name = $wpdb->prefix . 'new_table';
			if(!empty($mtwpvalu))
			{
				foreach($mtwpvalu as $r) {	 
					  echo "<p>".$r->text_name. "</ br>" .$r->main."</p>"; 
				 }
			} else {
				 echo "<p>Boo, we couldn't find anything that is in all these groups. Try removing a category!</p>";	 	 
					} 
}



///////////////////////////



add_action('admin_menu', 'wp_myCustomPlugin_add_pages');
register_activation_hook( __FILE__, 'wp_myCustomPlugin_DB' );
?>