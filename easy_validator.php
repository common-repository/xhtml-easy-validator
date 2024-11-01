<?php
/*
Plugin Name: (x)html easy validator 
Plugin URI:
Description: Check the doctype validity using W3c validator (hml , xhtml, ...) when you create or update your page / post / custom post type and show the result in backend.
Now W3C validity can be check from local server too. 
Version: 0.4
Author: Nicolas ANDRÉ AKA Nikoya
Author URI:


Copyright 2011  Nikoya  (nico.andre.info@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 *
 *Verifie que l'on est dans le panneau de control
 */
if(is_admin()){
	
/**
 *
 * INSTALLATION
 *
 * ajoute pour chaque page / post / custom post une meta value '_easy_validatore_result' avec la valeur 'UNCHECKED'
 */
register_activation_hook( __FILE__, 'install_easy_validator');

function install_easy_validator(){

	add_action('admin_notices', 'showAdminMessages');

    //Articles	(ajoute '_easy_validatore_result')
    $myposts = get_posts( $args );
    foreach( $myposts as $post ) :	
            add_post_meta($post->ID, '_easy_validatore_result',  'UNCHECKED', true);
    endforeach;
   
   //Pages (ajoute '_easy_validatore_result')
    $myposts=get_pages ($args); 
    foreach( $myposts as $post ) :	
            add_post_meta($post->ID, '_easy_validatore_result',  'UNCHECKED', true);
    endforeach;
    
    // Custom post type (liste les custiom post type)
    $args=array(
        'public'   => true,
        '_builtin' => false

    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator);
    foreach ($post_types  as $post_type ) {
    
    
        $args = array('post_type' => $post_type, 'showposts' => -1);
        $the_query = new WP_Query( $args );
        // The Loop
        while ( $the_query->have_posts() ) : $the_query->the_post();
        //the_ID(); echo '  '.the_title();
        add_post_meta(get_the_ID(), '_easy_validatore_result',  'UNCHECKED', true);
        endwhile;
        // Reset Post Data
        wp_reset_postdata();

    }


	
}


/**
 *
 * UNINSTALL
 *
 *  Supprime toutes les meta_value commencant par '_easy_validatore'
 *  La Bdd est propre 
 */
register_uninstall_hook( __FILE__, 'uninstall_easy_validator');

function uninstall_easy_validator(){
	global $wpdb;
		
	$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."postmeta` WHERE `meta_key` LIKE '_easy_validatore%%'"));
		
	delete_option('xhtml_easy_validator_method');

}



/**
 *
 *
 * CREATION MENU ADMINNISTRATION
 *
 */
add_action('admin_menu', 'xhtml_easy_validator_menu');

function xhtml_easy_validator_menu() {
	global $xhtml_easy_validator_page;
	
	$xhtml_easy_validator_page = add_options_page('(x)html easy validator', '(x)html easy validator', 'manage_options', '(x)html-easy-validator', 'xhtml_easy_validator_options');
	
	
//	add_action( 'admin_head-'.$xhtml_easy_validator_page, 'myeasytest' );


	
	
	
	
}
/*
function myeasytest(){
	
	//echo 'tac';
	
	
	if(is_numeric($_GET['easy_validator'])){
		$easy_validator_id      = intval($_GET['easy_validator']);
		$checkthis_url          = get_permalink($easy_validator_id);
		$post_data              = htmlspecialchars(file_get_contents($checkthis_url));
		echo '
		<form method="post" enctype="multipart/form-data" action="http://validator.w3.org/check" id="w3check">
		<textarea id="fragment" name="fragment" rows="12" cols="80">'.$post_data.'</textarea>
		<input title="Submit for validation" type="submit" value="Check" >
		</form>
		<script type="text/javascript">
		// <![CDATA[
		document.forms["w3check"].submit();
		// ]]>
		</script>';

	}

	
	
}
*/

function xhtml_easy_validator_options(){
	
	//enregsitre la methode de connexion a la w3c
	if($_POST['method']=='online'){
		update_option('xhtml_easy_validator_method', $_POST['method']);
	}elseif($_POST['method']=='offline'){
		update_option('xhtml_easy_validator_method', $_POST['method']);		
	
	}
	
	//Affiche les options de connexion en fonction de ce qui à deja été enregistré dans la bdd
	if(get_option('xhtml_easy_validator_method')=='offline'){
		
		$form_method = '
				<input type="radio" name="method" value="online"  />online
				<input type="radio" name="method" value="offline" checked/> offline
				';
	}else{
		
		$form_method = '
				<input type="radio" name="method" value="online"  checked/>online
				<input type="radio" name="method" value="offline" /> offline
				';	
	}
	
	
	echo '<a href="http://validator.w3.org/"><img style="float:left;" src="'.WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'w3c.png" alt="logo w3c" /></a><h2 style="float:left;">(x)html easy validator</h2>
	
<p style="clear:both;">If your web site is not accessible from internet check "offline" (for example if you are working on a local post).</p>
<form action="options-general.php?page=(x)html-easy-validator" method="post">
'.$form_method.'<br />
<input type="submit" name="submit" id="submit" class="button-primary" value="Enregistrer les modifications">
</form>';

echo '<hr />';


	if(is_numeric($_GET['easy_validator'])){
		$easy_validator_id      = intval($_GET['easy_validator']);
		$checkthis_url          = get_permalink($easy_validator_id);
		$post_data              = htmlspecialchars(file_get_contents($checkthis_url));

	}
		echo '<h2>Validate source from W3c</h2>
		<form method="post" enctype="multipart/form-data" action="http://validator.w3.org/check" id="w3check" target="_extern">
		<textarea id="fragment" name="fragment" rows="12" cols="80">'.$post_data.'</textarea>
		<input title="Submit for validation" type="submit" value="Check" >
		</form>
		';
		if(!empty($post_data)){
			
			echo '
				<script type="text/javascript">
				// <![CDATA[
				document.forms["w3check"].submit();
				// ]]>
				</script>
			';

		}

}




/**
 *
 * Ajout lien vers options depuis menu des plugins
 *
 */

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'settings_link');
// Add settings option
function settings_link($links) {
	$new_links = array();
	
	$new_links[] = '<a href="options-general.php?page=(x)html-easy-validator">Settings</a>';
	
	return array_merge($new_links, $links);
}


/*************************************************************************************************************************************/

/**
 *
 * PRINCIPALE FONCTION
 * Effectue la connexion a la W3C
 * Enregistre les reponses
 */

function check_validator(){
  
    global $post;



    // Si la page est public ou qu'elle vient d'etre crée en public et qu'elle ne posse de pass alors on la control  
    if((($post->post_status == 'publish') or ($post->post_status == 'auto-draft') or ($post->post_status == 'draft')) && (empty($post->post_password) or !isset($post->post_password))){
    
        //Recupeartion de l'id de la page courante
        $post_id 	=	get_the_ID();
        //Recupeartion de l'url de la page courante
        $checkthis 	=	get_permalink();

		
		//Si le server n'est pas accessible depuis l'exterieur
		if(get_option('xhtml_easy_validator_method') == 'offline'){
			
			/* PAGE LOCALE */		
					
				// Url de connexion a l'API W3C
			$url = 'http://validator.w3.org/check';
			
			//Recupere et encode la source de la page locale a tester
			$test = urlencode(file_get_contents($checkthis));
			
			
			//Parametre la connexion a l'api de la W3C en method post pour get_headers
			$alternate_opts = array(
				'http'=>array(
					'method'=>"POST",
					'header'=>"Content-type: application/x-www-form-urlencoded\r\n" .
					"Content-length: " . strlen("fragment=".$test),
					'content'=>"fragment=".$test
				)
			);
			stream_context_get_default($alternate_opts);			
					
			/*FIN PAGE LOCALE */		
		//Sinon le server est accessible depuis l'exterieur
		}else{
			
        //Création du lien vers l'ap W3C    
        $url = 'http://validator.w3.org/check?uri='.$checkthis;      
		
		}
		
		
        // Connexion a l'API W3C et recuperation du header renvoyé  pour la page / post ou custom post type courrant
        $headers = get_headers($url, 1);
				
		//var_dump($headers);
		
        $X_W3C_Validator_Status     = $headers['X-W3C-Validator-Status'];
        $X_W3C_Validator_Warnings   = $headers['X-W3C-Validator-Warnings'];
        $X_W3C_Validator_Errors     = $headers['X-W3C-Validator-Errors'];
        
        // Analyse et enregistre les données issuent de W3C
        
        if($X_W3C_Validator_Status=='Abort')
            {
                //enregistre problem de connexion 
            $result='SERVER ERROR';
            // enregistre
            update_post_meta($post_id, '_easy_validatore_result',  $result);
            // A FAIRE : Verifier si d'autre most meta exist dan la bdd et les supprimer
                
            return;
        
            }elseif($X_W3C_Validator_Status=='Invalid')
            {

            // enregistre
            update_post_meta($post_id, '_easy_validatore_result',  $X_W3C_Validator_Status);
            update_post_meta($post_id, '_easy_validatore_Errors', $X_W3C_Validator_Errors);
            update_post_meta($post_id, '_easy_validatore_Warnings', $X_W3C_Validator_Warnings);
           
                
            }elseif($X_W3C_Validator_Status=='Valid')
            {
                
            // enregistre
            update_post_meta($post_id, '_easy_validatore_result',  $X_W3C_Validator_Status);
            update_post_meta($post_id, '_easy_validatore_Errors', $X_W3C_Validator_Errors);
            update_post_meta($post_id, '_easy_validatore_Warnings', $X_W3C_Validator_Warnings);
        
            }else{
                
            // probleme reseau (w3c injoignable par exemple)
            $result='NETWORK PROBLEM';
            // enregistre
            update_post_meta($post_id, '_easy_validatore_result',  $result);
        
            }
    }
    

}


//Lance la fonction de validation W3c a l'enregistrement d'une page / post ou post type
add_action('publish_page' , 'check_validator');
add_action('publish_post' , 'check_validator');

function check_custom(){    // Custom post type (liste les custom post type)
    $args=array(
        'public'   => true,
        '_builtin' => false

    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator);
    foreach ($post_types  as $post_type ) {

add_action('publish_'.$post_type, 'check_validator');
	}
}

//a l'initialisation de l'admin on liste les custom
add_action('admin_init', 'check_custom');

/*************************************************************************************************************************************/

/**
 *
 * AFFICHAGE DU STATUS W3C DE CHAQUE PUBLICATION
 * affiche les resultats enregistrés et les rends triables
 *
 */


// Enregistre les colonnes 
function validator_column_register( $columns ) {
	$columns['easy-validator'] = __( 'easy-validator (w3c)', 'easy-validator' );
 
	return $columns;
}
add_filter( 'manage_edit-post_columns', 'validator_column_register' );
add_filter( 'manage_posts_columns', 'validator_column_register' );
add_filter( 'manage_pages_columns', 'validator_column_register' );



// Affiche le contenu des colonnes
function validator_column_display( $column_name, $post_id ) {
	global $post;
	global $xhtml_easy_validator_page;
	if ( 'easy-validator' != $column_name )
		return;
	
	// si le post est privé ou protegé par mot de pass on l'affiche dans la colonne et on stop tout
	if($post->post_status != 'publish' or !empty($post->post_password)){
		echo 'post locked';
		return;
	}
	

	
	//Modifie le lien selon si le site est accessible depuis le net ou non
	if(get_option('xhtml_easy_validator_method')=='offline'){
		//$w3clink='edit.php?easy_validator=2';
		//$w3clink = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'w3c_local_redirect.php?easy_validator='.$post->ID;
		
					
					
		$w3clink = 'options-general.php?page=(x)html-easy-validator&easy_validator='.$post->ID;
									   
	}else{
		$w3clink='http://validator.w3.org/check?uri='.get_permalink();
	}
	

        if(get_post_meta($post->ID, '_easy_validatore_result', true)=='Valid'){
			
		  	echo '<h3 style="float:left;"><a style="color:#55b05a;" href="'.$w3clink.'" target="_extern">W3C</a></h2>';
				
			if(get_post_meta($post->ID, '_easy_validatore_Warnings', true) != 0){
				echo '<p style="float:left; margin-left:5px; margin-top:5px;"><a style="color:#c1c113;" href="'.$w3clink.'"  target="_extern"> Warnings : '.get_post_meta($post->ID, '_easy_validatore_Warnings', true).'</a></p>';
			}

		}elseif(get_post_meta($post->ID, '_easy_validatore_result', true)=='UNCHECKED'){
			
			echo '<h3><a href="'.$w3clink.'" style="color:#606060;" target="_extern">W3C ?</a></h2>';
		}elseif(get_post_meta($post->ID, '_easy_validatore_result', true)=='SERVER ERROR'){
			echo '<h3 style="float:left;"><a style="color:#d23d24;" href="'.$w3clink.'"  target="_extern">W3C</a></h2>
			<p style="float:left; margin-left:5px; margin-top:5px;"><a style="color:#d23d24;" href="'.$w3clink.'"  target="_extern">SERVER ERROR <br />Try to change <a href="options-general.php?page=(x)html-easy-validator" target="_extern">Settings validator</a></p>';

        }else{
			
			echo '<h3 style="float:left;"><a style="color:#d23d24;" href="'.$w3clink.'" target="_extern">W3C</a></h2>
				<p style="float:left; margin-left:5px; margin-top:5px;"><a style="color:#d23d24;" href="'.$w3clink.'" target="_extern">Errors : '.get_post_meta($post->ID, '_easy_validatore_Errors', true).' <br /> Warnings : '.get_post_meta($post->ID, '_easy_validatore_Warnings', true).'</a></p>';
				
            }
        
}

add_action( 'manage_posts_custom_column', 'validator_column_display', 10, 2 );
add_action( 'manage_pages_custom_column', 'validator_column_display', 10, 2 );


// Enresgistre les colonnes comme triable pour les pages et les posts
function validator_column_register_sortable( $columns ) {
	$columns['easy-validator'] = '_easy_validatore_result';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'validator_column_register_sortable' );
add_filter( 'manage_edit-page_sortable_columns', 'validator_column_register_sortable' );

// Enresgistre les cononnes comme triable pour les customs post type
function add_sortable_views_for_custom_post_types(){
   $args=array(
     'public'   => true,
     '_builtin' => false
   );
   $post_types=get_post_types($args);
   foreach ($post_types  as $post_type ) {
      add_filter( 'manage_edit-'.$post_type.'_sortable_columns', 'validator_column_register_sortable' );
   }
}
add_action('wp', 'add_sortable_views_for_custom_post_types');
	

// Gere l'affichage trié des colonnes
function views_column_orderby( $vars ) {
  if ( isset( $vars['orderby'] ) && '_easy_validatore_result' == $vars['orderby'] ) {
      $vars = array_merge( $vars, array(
         'meta_key' => '_easy_validatore_result',
         'orderby' => 'meta_value'
      ) );
   }

   return $vars;
}
add_filter( 'request', 'views_column_orderby' );


}// FIN verificacion globale is_admin