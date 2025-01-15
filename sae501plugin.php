<?php
/*
*Plugin Name:Sae501
 * Plugin URI:https://jsg4704a.mmiweb.iut-tlse3.fr/wp-content/plugins/sae501/sae501plugin.pho
* Description: Un plugin pour la gestion de projet de cours mmi
* Version: 1.0
* Author:Ogené JOSEPH
* Author URI:https://ojoseph.fr/
*/
//CREATION DE FILTRES
function hack_title( $title ) {
    return $title."  vous avez été hacké par O.J";
}
add_filter( 'the_title', 'hack_title' );

//CREATION DES LOGS DE SAUVREGARDE
function enregistrer_log_sauvegarde($post_id) {
    $log_message = "Le post avec l'ID $post_id a été sauvegardé.\n";
    file_put_contents( __DIR__ . '/save_log.txt', $log_message, FILE_APPEND );
}
add_action( 'save_post', 'enregistrer_log_sauvegarde' );


// ACTIVATION DU PLUGIN
function saePluginActivation() {
    // Ajouter ici des actions à effectuer lors de l'activation
        $msg="le plugin a bien été activé";
    Bugfu::log($msg);
	//ajout des roles
	$role = get_role('administrator');
  
        $capabilities = array(

			 'read_projetmmi',
			'read_private_projetmmis',
		
			'edit_projetmmi',
			'edit_projetmmis',
           	'edit_others_projetmmis',
			'edit_private_projetmmis',
			'edit_published_projetmmis',
		
			'publish_projetmmis',
		
            'delete_projetmmi',
            'delete_projetmmis',
			'delete_published_projetmmis',
           	'delete_private_projetmmis',
         
        );
        foreach ($capabilities as $cap) {
            $role->add_cap($cap);
        }
    
	add_role('profIUTC','prof IUTC', $capabilities=array(
		
		 	'read_projetmmi'=>true,
			'read_private_projetmmis'=>true,
		
			'edit_projetmmi'=>true,
			'edit_projetmmis'=>true,
           	'edit_others_projetmmis'=>true,
			'edit_private_projetmmis'=>true,
			'edit_published_projetmmis'=>true,
		
			'publish_projetmmis='>true,
		
            'delete_projetmmi'=>true,
            'delete_projetmmis'=>true,
			'delete_published_projetmmis'=>true,
           	'delete_private_projetmmis'=>true,
		
			'upload_files'=>true,
			'manage_categories'=>true,
			'manage_links'=>true,
			'moderate_comments'=>true,
			'unfiltered_html'=>true
           
            
	));
	add_role('eleveIUTC','élève IUTC', $capabilities=array(
		
			
            'read_projetmmi'=>true,
			'read_private_projetmmis'=>false,
		
            'delete_projetmmi'=>true,
			'delete_projetmmis'=>false,
			'delete_others_projetmmis'=>false,
		
			'edit_projetmmi'=>true,
            'edit_projetmmis'=>true,
            'edit_others_projetmmis'=>false,
            
           	'publish_projetmmis='>false,
           
            
	));
	//creation des options
	add_option('Projets_option','custom');
}
// Hook pour l'activation du plugin
register_activation_hook(__FILE__, 'saePluginActivation');




// DESACTIVATION DU PLUGIN
function saePluginDesctivation() {
    // Ajouter ici des actions à effectuer lors de la désactivation
    $msg="le plugin a bien été desactivé";
    Bugfu::log($msg);
	remove_role('eleveIUTC');
	remove_role('AdminIUTC');
	remove_role('profIUTC');
}


// Hook pour la désactivation du plugin
register_deactivation_hook(__FILE__, 'saePluginDesctivation');
// DESINSTALLATION DU PLUGIN 
function saePluginUninstall() {
    // Ajouter ici des actions à effectuer lors de la désinstallation
    $msg="le plugin a bien été desinstallé";
    Bugfu::log($msg);
}
// Hook pour la désinstallation du plugin
register_uninstall_hook(__FILE__, 'saePluginUninstall');



//CREATION DU CPT
	function mmiprojet_custom_post_type() {
    	register_post_type('projetsmmi',
       		 array(
           		 'label' => 'ProjetsMMI',
         		  'public' => true,
				'capability_type'=>'projetmmi'
     	   		)
   		 	);
	}
add_action('init', 'mmiprojet_custom_post_type');	
add_post_type_support('projetsmmi',array('title','editor','author','thumbnail','excerp','trackbacks','post-formats','revisions'));

//CREATION DE SES TAXONOMIES
  function create_taxonomy(){
register_taxonomy( 'cours', 'projetsmmi', array(
'label'=>'cours',
	'hierarchical'=>true
));}

add_action('init', 'create_taxonomy');
//CREATION DES CHAMPS PERSONNALISE
function ajouter_meta_box_github_projet() {
    add_meta_box(
        'github_link_metabox',             // ID de la meta box
        'Lien GitHub',                     // Titre de la meta box
        'afficher_meta_box_github',        // Fonction de callback
       
    );
}
add_action('add_meta_boxes', 'ajouter_meta_box_github_projet');

function afficher_meta_box_github($post) {
    // Récupérer la valeur actuelle du champ (s'il existe déjà)
    $github_link = get_post_meta($post->ID, '_github_link', true);
    ?>
    <label for="github_link">Lien GitHub :</label>
    <input type="url" name="github_link" id="github_link" value="<?php echo esc_attr($github_link); ?>" style="width: 100%;" placeholder="https://github.com/votre-projet">
    <?php
}



//CREATION DE L'OUTIL DE VISUALISATION DES ROLES POUR LE CPT PROJETSMMI

add_action( 'admin_menu', 'wp_learn_submenu', 11 );
function wp_learn_submenu() {
    add_submenu_page(
        'tools.php',
        esc_html__( 'WP Learn Story CPT', 'wp_learn' ),
        esc_html__( 'WP Learn Story CPT', 'wp_learn' ),
        'manage_options',
        'wp_learn_story_cpt',
        'wp_learn_render_story_cpt'
    );
}
function wp_learn_render_story_cpt() {
    $story = $GLOBALS['wp_post_types']['projetsmmi']
    ?>
    <div class="wrap" id="wp_learn_admin">
        <h1>MMI projet</h1>
        <pre style="font-size: 22px; line-height: 1.2;"><?php print_r( array( 'capability_type' => $story->capability_type ) ) ?></pre>
        <pre style="font-size: 22px; line-height: 1.2;"><?php print_r( array( 'map_meta_cap' => $story->map_meta_cap ) ) ?></pre>
        <pre style="font-size: 22px; line-height: 1.2;"><?php print_r( array( 'cap' => $story->cap ) ) ?></pre>
        <pre style="font-size: 22px; line-height: 1.2;"><?php print_r( $story ) ?></pre>
    </div>
    <?php
}

// CREATION D'UNE PAGE DE REGLAGES
/**
 * Fonction pour enregistrer une nouvelle page de réglages dans le menu d'administration WordPress.
 */
function projetmmi_register_options_page() {
    // Ajoute une page de réglages dans le menu d'administration.
    add_menu_page(
        'Réglages projetmmi',           // Titre de la page.
        'Réglages projetmmi',           // Nom affiché dans le menu.
        'manage_options',         // Capacité requise pour accéder à cette page (seulement accessible aux administrateurs).
        'projetmmi_settings',           // Slug de la page (utilisé dans l'URL).
        'projetmmi_options_page_html'   // Fonction de rappel pour afficher le contenu de la page.
    );
}

// Ajoute l'action pour créer la page de réglages lorsque le menu d'administration est chargé.
add_action('admin_menu', 'projetmmi_register_options_page');

/**
 * Fonction pour initialiser les réglages, sections et champs de la page de réglages.
 */
function projetmmi_settings_init() {
    // Enregistre un nouveau réglage dans la base de données.
    // 'sae_settings_group' est l'identifiant du groupe, et 'sae_custom_option' est le nom de l'option.
    register_setting('projetmmi_settings_group', 'projetmmi_custom_option');

    // Ajoute une section de réglages dans la page.
    add_settings_section(
        'projetmmi_section',            // ID de la section.
        'projetmmi SAE',            // Titre de la section.
        'projetmmi_section_callback',   // Fonction de rappel pour afficher du texte ou des instructions dans la section.
        'projetmmi_settings'            // Slug de la page où la section est ajoutée.
    );

    // Ajoute un champ de formulaire pour l'option dans la section de réglages.
    add_settings_field(
        'projetmmi_field',              // ID du champ.
        'Champ texte',            // Label du champ affiché à gauche.
        'projetmmi_field_callback',     // Fonction de rappel pour afficher le champ de formulaire.
        'projetmmi_settings',           // Slug de la page de réglages.
        'projetmmi_section'             // ID de la section où le champ est ajouté.
    );
}

// Ajoute l'action pour initialiser les réglages lorsque l'administration est initialisée.
add_action('admin_init', 'projetmmi_settings_init');

/**
 * Fonction de rappel pour la section de réglages.
 * Affiche une description ou des instructions pour la section.
 */
function projetmmi_section_callback() {
    echo '<p>Paramètres personnalisés pour le plugin projetmmi.</p>';
}

/**
 * Fonction de rappel pour le champ de formulaire.
 * Affiche un champ texte et récupère la valeur actuelle de l'option depuis la base de données.
 */
function projetmmi_field_callback() {
    // Récupère la valeur de l'option 'sae_custom_option' dans la base de données.
    $value = get_option('projetmmi_custom_option');
    ?>
    <!-- Champ texte pour saisir la valeur de l'option -->
    <input type="text" name="projetmmi_custom_option" value="<?php echo esc_attr($value); ?>">
    <?php
}

/**
 * Fonction de rappel pour afficher le contenu de la page de réglages.
 */
function projetmmi_options_page_html() {
    // Vérifie que l'utilisateur a les permissions nécessaires pour gérer les options.
    if (!current_user_can('manage_options')) { return;}
// Affiche un message de confirmation si les réglages ont été sauvegardés.
    if (isset($_GET['settings-updated'])) {
		add_settings_error('projetmmi_messages', 'projetmmi_message', 'Réglages sauvegardés','updated');
    }

    // Affiche les messages d'erreur ou de succès.
    settings_errors('projetmmi_messages');
    ?>
    <!-- Contenu HTML de la page de réglages -->
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // Affiche les champs de sécurité pour la sauvegarde des réglages.
            settings_fields('projetmmi_settings_group');
            // Affiche les sections et les champs enregistrés pour la page 'sae_settings'.
            do_settings_sections('projetmmi_settings');
            // Bouton pour sauvegarder les réglages.
            submit_button('Enregistrer les réglages');
            ?>
        </form>
    </div>
    <?php
}









?>